#!/usr/bin/env php
<?php

$sequenceDirectory = '/var/www/localhost/htdocs/straindatabase/sequence_files';
$outputFile = 'missing_sequence_files.tsv';
$databaseHost = 'localhost';
$databaseName = 'straindatabase';
$databaseUser = 'readonly';

function usage(): void {
  global $sequenceDirectory, $outputFile, $databaseHost, $databaseName, $databaseUser;

  echo <<<USAGE
Usage: php scripts/list_missing_sequence_files.php [options]

List allele/plasmid records that name a sequence file in SQL but do not have a
usable file on disk.

Defaults:
  Sequence files: $sequenceDirectory
  Output file:    $outputFile
  MySQL host:     $databaseHost
  MySQL database: $databaseName
  MySQL user:     $databaseUser

Options:
  --sequence-dir DIR   Directory containing sequence files.
  --output FILE        TSV output file. Use "-" for stdout.
  --host HOST          MySQL host.
  --database NAME      MySQL database name.
  --user USER          MySQL user.
  --help              Show this help.

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
    case '--output':
      $outputFile = $argv[++$i] ?? '';
      if ($outputFile === '') {
        fwrite(STDERR, "Missing value for --output\n");
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

function cleanFilename(?string $filename): string {
  return trim((string)$filename);
}

function isSafeSequenceFilename(string $filename): bool {
  return $filename !== '' && $filename !== '.' && $filename !== '..' && strpbrk($filename, "/\\\0") === false;
}

function sequencePath(string $sequenceDirectory, string $filename): string {
  return rtrim($sequenceDirectory, '/') . '/' . $filename;
}

function missingReason(string $sequenceDirectory, string $filename): string {
  if ($filename === '') {
    return 'no_filename_in_database';
  }
  if (!isSafeSequenceFilename($filename)) {
    return 'invalid_filename_in_database';
  }

  $path = sequencePath($sequenceDirectory, $filename);
  if (!is_file($path)) {
    return 'missing_on_disk';
  }
  if (!is_readable($path)) {
    return 'not_readable_on_disk';
  }

  return '';
}

function sequenceDataState(?string $sequenceData): array {
  $hasData = $sequenceData !== null && $sequenceData !== '';

  return [
    'sql_has_sequence_data' => $hasData ? 'yes' : 'no',
    'sql_sequence_bytes' => $hasData ? strlen($sequenceData) : 0,
    'sql_sequence_sha256' => $hasData ? hash('sha256', $sequenceData) : '',
  ];
}

function collectMissingRows(PDO $db, string $sequenceDirectory, array $config): array {
  $sql = "SELECT {$config['id_column']} AS entity_id, {$config['name_column']} AS entity_name, sequenceDataName_col, sequence_data_col FROM {$config['table']} WHERE sequenceDataName_col IS NOT NULL AND sequenceDataName_col != '' ORDER BY {$config['id_column']}";
  $statement = $db->prepare($sql);
  $statement->execute();

  $rows = [];
  while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $filename = cleanFilename($row['sequenceDataName_col'] ?? '');
    $reason = missingReason($sequenceDirectory, $filename);
    if ($reason === '') {
      continue;
    }

    $sqlState = sequenceDataState($row['sequence_data_col'] ?? null);
    $rows[] = [
      'entity_type' => $config['entity_type'],
      'id' => $row['entity_id'],
      'name' => $row['entity_name'],
      'sequence_filename' => $filename,
      'missing_reason' => $reason,
      'expected_path' => isSafeSequenceFilename($filename) ? sequencePath($sequenceDirectory, $filename) : '',
      'sql_has_sequence_data' => $sqlState['sql_has_sequence_data'],
      'sql_sequence_bytes' => $sqlState['sql_sequence_bytes'],
      'sql_sequence_sha256' => $sqlState['sql_sequence_sha256'],
    ];
  }

  return $rows;
}

$db = connectDatabase($databaseHost, $databaseName, $databaseUser);
$rows = array_merge(
  collectMissingRows($db, $sequenceDirectory, [
    'entity_type' => 'allele',
    'table' => 'allele_table',
    'id_column' => 'allele_id',
    'name_column' => 'alleleName_col',
  ]),
  collectMissingRows($db, $sequenceDirectory, [
    'entity_type' => 'plasmid',
    'table' => 'plasmid_table',
    'id_column' => 'plasmid_id',
    'name_column' => 'plasmidName_col',
  ])
);

$handle = $outputFile === '-' ? STDOUT : fopen($outputFile, 'w');
if ($handle === false) {
  fwrite(STDERR, "Could not write output file: $outputFile\n");
  exit(1);
}

writeTsvRow($handle, [
  'entity_type',
  'id',
  'name',
  'sequence_filename',
  'missing_reason',
  'expected_path',
  'sql_has_sequence_data',
  'sql_sequence_bytes',
  'sql_sequence_sha256',
]);

foreach ($rows as $row) {
  writeTsvRow($handle, [
    $row['entity_type'],
    $row['id'],
    $row['name'],
    $row['sequence_filename'],
    $row['missing_reason'],
    $row['expected_path'],
    $row['sql_has_sequence_data'],
    $row['sql_sequence_bytes'],
    $row['sql_sequence_sha256'],
  ]);
}

if ($outputFile !== '-') {
  fclose($handle);
  echo "Wrote $outputFile\n";
}
echo "Missing sequence file references: " . count($rows) . "\n";

