#!/usr/bin/env php
<?php

$sequenceDirectory = '/var/www/localhost/htdocs/straindatabase/sequence_files';
$outputDirectory = getcwd();
$databaseHost = 'localhost';
$databaseName = 'straindatabase';
$databaseUser = 'readonly';

function usage(): void {
  global $sequenceDirectory, $outputDirectory, $databaseHost, $databaseName, $databaseUser;

  echo <<<USAGE
Usage: php scripts/audit_sequence_storage.php [options]

Build read-only TSV audits comparing allele/plasmid SQL sequence_data_col values
with sequence files on disk.

Defaults:
  Sequence files: $sequenceDirectory
  Output dir:      $outputDirectory
  MySQL host:      $databaseHost
  MySQL database:  $databaseName
  MySQL user:      $databaseUser

Options:
  --sequence-dir DIR   Directory containing sequence files.
  --output-dir DIR     Directory for TSV output files.
  --host HOST          MySQL host.
  --database NAME      MySQL database name.
  --user USER          MySQL user.
  --help              Show this help.

Outputs:
  sequence_audit_alleles.tsv
  sequence_audit_plasmids.tsv
  sequence_audit_duplicate_filenames.tsv

USAGE;
}

for ($i = 1; $i < $argc; $i++) {
  switch ($argv[$i]) {
    case '--sequence-dir':
      $sequenceDirectory = $argv[++$i] ?? '';
      if ($sequenceDirectory === '') {
        fwrite(STDERR, "Missing value for --sequence-dir\n");
        exit(2);
      }
      break;
    case '--output-dir':
      $outputDirectory = $argv[++$i] ?? '';
      if ($outputDirectory === '') {
        fwrite(STDERR, "Missing value for --output-dir\n");
        exit(2);
      }
      break;
    case '--host':
      $databaseHost = $argv[++$i] ?? '';
      if ($databaseHost === '') {
        fwrite(STDERR, "Missing value for --host\n");
        exit(2);
      }
      break;
    case '--database':
      $databaseName = $argv[++$i] ?? '';
      if ($databaseName === '') {
        fwrite(STDERR, "Missing value for --database\n");
        exit(2);
      }
      break;
    case '--user':
      $databaseUser = $argv[++$i] ?? '';
      if ($databaseUser === '') {
        fwrite(STDERR, "Missing value for --user\n");
        exit(2);
      }
      break;
    case '--help':
      usage();
      exit(0);
    default:
      fwrite(STDERR, "Unknown option: {$argv[$i]}\n");
      usage();
      exit(2);
  }
}

if (!is_dir($sequenceDirectory) || !is_readable($sequenceDirectory)) {
  fwrite(STDERR, "Sequence directory is not readable: $sequenceDirectory\n");
  exit(1);
}

if (!is_dir($outputDirectory) || !is_writable($outputDirectory)) {
  fwrite(STDERR, "Output directory is not writable: $outputDirectory\n");
  exit(1);
}

function cleanFilename(?string $filename): string {
  return trim((string)$filename);
}

function sequencePath(string $sequenceDirectory, string $filename): string {
  return rtrim($sequenceDirectory, '/') . '/' . $filename;
}

function diskState(string $sequenceDirectory, string $filename): array {
  if ($filename === '') {
    return [
      'disk_exists' => 'no_filename',
      'disk_readable' => 'no',
      'disk_bytes' => '',
      'disk_sha256' => '',
    ];
  }

  $path = sequencePath($sequenceDirectory, $filename);
  if (!is_file($path)) {
    return [
      'disk_exists' => 'no',
      'disk_readable' => 'no',
      'disk_bytes' => '',
      'disk_sha256' => '',
    ];
  }

  if (!is_readable($path)) {
    return [
      'disk_exists' => 'yes',
      'disk_readable' => 'no',
      'disk_bytes' => filesize($path),
      'disk_sha256' => '',
    ];
  }

  return [
    'disk_exists' => 'yes',
    'disk_readable' => 'yes',
    'disk_bytes' => filesize($path),
    'disk_sha256' => hash_file('sha256', $path),
  ];
}

function sqlState(?string $sequenceData): array {
  $hasData = ($sequenceData !== null && $sequenceData !== '');

  return [
    'sql_has_sequence_data' => $hasData ? 'yes' : 'no',
    'sql_bytes' => $hasData ? strlen($sequenceData) : 0,
    'sql_sha256' => $hasData ? hash('sha256', $sequenceData) : '',
  ];
}

function classifyState(array $sql, array $disk): string {
  if ($sql['sql_has_sequence_data'] === 'yes' && $disk['disk_exists'] === 'yes' && $disk['disk_readable'] === 'yes') {
    return $sql['sql_sha256'] === $disk['disk_sha256'] ? 'sql_and_disk_match' : 'sql_and_disk_differ';
  }
  if ($sql['sql_has_sequence_data'] === 'yes' && $disk['disk_exists'] === 'no') {
    return 'sql_only_disk_missing';
  }
  if ($sql['sql_has_sequence_data'] === 'yes' && $disk['disk_exists'] === 'no_filename') {
    return 'sql_only_no_filename';
  }
  if ($sql['sql_has_sequence_data'] === 'yes' && $disk['disk_readable'] === 'no') {
    return 'sql_present_disk_unreadable';
  }
  if ($sql['sql_has_sequence_data'] === 'no' && $disk['disk_exists'] === 'yes' && $disk['disk_readable'] === 'yes') {
    return 'disk_only';
  }
  if ($sql['sql_has_sequence_data'] === 'no' && $disk['disk_exists'] === 'yes') {
    return 'disk_unreadable';
  }
  if ($sql['sql_has_sequence_data'] === 'no' && $disk['disk_exists'] === 'no') {
    return 'filename_only_disk_missing';
  }
  return 'no_sequence';
}

function writeTsvRow($handle, array $row): void {
  fputcsv($handle, $row, "\t", '"', "\\");
}

function promptForPassword(string $user, string $host): string {
  fwrite(STDERR, "MySQL password for $user@$host: ");
  $canHideInput = function_exists('shell_exec') && function_exists('stream_isatty') && stream_isatty(STDIN);
  if ($canHideInput) {
    shell_exec('stty -echo');
  }
  $password = fgets(STDIN);
  if ($canHideInput) {
    shell_exec('stty echo');
  }
  fwrite(STDERR, "\n");

  return rtrim((string)$password, "\r\n");
}

function connectDatabase(string $host, string $database, string $user): PDO {
  $password = promptForPassword($user, $host);
  $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";

  try {
    return new PDO($dsn, $user, $password, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
  } catch (PDOException $e) {
    fwrite(STDERR, "Could not connect to MySQL as $user@$host for database $database: {$e->getMessage()}\n");
    exit(1);
  }
}

function auditTable(PDO $db, string $sequenceDirectory, string $outputPath, array $config, array &$filenames): array {
  $handle = fopen($outputPath, 'w');
  if ($handle === false) {
    throw new RuntimeException("Could not write $outputPath");
  }

  writeTsvRow($handle, [
    'entity_type',
    'id',
    'name',
    'sequence_filename',
    'sql_has_sequence_data',
    'sql_bytes',
    'sql_sha256',
    'disk_exists',
    'disk_readable',
    'disk_bytes',
    'disk_sha256',
    'state',
  ]);

  $sql = "SELECT {$config['id_column']} AS entity_id, {$config['name_column']} AS entity_name, sequenceDataName_col, sequence_data_col FROM {$config['table']} ORDER BY {$config['id_column']}";
  $statement = $db->prepare($sql);
  $statement->execute();

  $summary = [];
  while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $filename = cleanFilename($row['sequenceDataName_col'] ?? '');
    $sqlInfo = sqlState($row['sequence_data_col'] ?? null);
    $diskInfo = diskState($sequenceDirectory, $filename);
    $state = classifyState($sqlInfo, $diskInfo);
    $summary[$state] = ($summary[$state] ?? 0) + 1;

    if ($filename !== '') {
      $filenames[$filename][] = [
        'entity_type' => $config['entity_type'],
        'id' => $row['entity_id'],
        'name' => $row['entity_name'],
      ];
    }

    writeTsvRow($handle, [
      $config['entity_type'],
      $row['entity_id'],
      $row['entity_name'],
      $filename,
      $sqlInfo['sql_has_sequence_data'],
      $sqlInfo['sql_bytes'],
      $sqlInfo['sql_sha256'],
      $diskInfo['disk_exists'],
      $diskInfo['disk_readable'],
      $diskInfo['disk_bytes'],
      $diskInfo['disk_sha256'],
      $state,
    ]);
  }

  fclose($handle);
  return $summary;
}

$db = connectDatabase($databaseHost, $databaseName, $databaseUser);
$allFilenames = [];

$alleleOutput = rtrim($outputDirectory, '/') . '/sequence_audit_alleles.tsv';
$plasmidOutput = rtrim($outputDirectory, '/') . '/sequence_audit_plasmids.tsv';
$duplicateOutput = rtrim($outputDirectory, '/') . '/sequence_audit_duplicate_filenames.tsv';

$alleleSummary = auditTable($db, $sequenceDirectory, $alleleOutput, [
  'entity_type' => 'allele',
  'table' => 'allele_table',
  'id_column' => 'allele_id',
  'name_column' => 'alleleName_col',
], $allFilenames);

$plasmidSummary = auditTable($db, $sequenceDirectory, $plasmidOutput, [
  'entity_type' => 'plasmid',
  'table' => 'plasmid_table',
  'id_column' => 'plasmid_id',
  'name_column' => 'plasmidName_col',
], $allFilenames);

$duplicateHandle = fopen($duplicateOutput, 'w');
if ($duplicateHandle === false) {
  throw new RuntimeException("Could not write $duplicateOutput");
}
writeTsvRow($duplicateHandle, ['sequence_filename', 'reference_count', 'references']);
foreach ($allFilenames as $filename => $references) {
  if (count($references) < 2) {
    continue;
  }
  $referenceStrings = array_map(
    static fn(array $reference): string => "{$reference['entity_type']}:{$reference['id']}:{$reference['name']}",
    $references
  );
  writeTsvRow($duplicateHandle, [$filename, count($references), implode('; ', $referenceStrings)]);
}
fclose($duplicateHandle);

echo "Wrote $alleleOutput\n";
echo "Wrote $plasmidOutput\n";
echo "Wrote $duplicateOutput\n";
echo "\nAllele summary:\n";
foreach ($alleleSummary as $state => $count) {
  echo "  $state: $count\n";
}
echo "\nPlasmid summary:\n";
foreach ($plasmidSummary as $state => $count) {
  echo "  $state: $count\n";
}
