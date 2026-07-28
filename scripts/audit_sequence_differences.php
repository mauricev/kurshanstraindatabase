#!/usr/bin/env php
<?php

$sequenceDirectory = '/var/www/localhost/htdocs/straindatabase/sequence_files';
$outputDirectory = getcwd();
$databaseHost = 'localhost';
$databaseName = 'straindatabase';
$databaseUser = 'readonly';
$sampleBytes = 80;

function usage(): void {
  global $sequenceDirectory, $outputDirectory, $databaseHost, $databaseName, $databaseUser, $sampleBytes;

  echo <<<USAGE
Usage: php scripts/audit_sequence_differences.php [options]

Build a deeper read-only comparison report for allele/plasmid sequence records
where SQL and disk may disagree.

Defaults:
  Sequence files: $sequenceDirectory
  Output dir:      $outputDirectory
  MySQL host:      $databaseHost
  MySQL database:  $databaseName
  MySQL user:      $databaseUser
  Sample bytes:    $sampleBytes

Options:
  --sequence-dir DIR   Directory containing sequence files.
  --output-dir DIR     Directory for TSV output files.
  --host HOST          MySQL host.
  --database NAME      MySQL database name.
  --user USER          MySQL user.
  --sample-bytes N     Bytes to include around first differing offset.
  --help              Show this help.

Outputs:
  sequence_difference_details.tsv
  sequence_difference_summary.tsv
  sequence_sql_only_recovery_manifest.tsv

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
    case '--sample-bytes':
      $sampleBytes = (int)($argv[++$i] ?? 0);
      if ($sampleBytes < 1) {
        fwrite(STDERR, "--sample-bytes must be greater than 0\n");
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

function sequencePath(string $sequenceDirectory, string $filename): string {
  return rtrim($sequenceDirectory, '/') . '/' . $filename;
}

function readDiskData(string $sequenceDirectory, string $filename): ?string {
  if ($filename === '') {
    return null;
  }

  $path = sequencePath($sequenceDirectory, $filename);
  if (!is_file($path) || !is_readable($path)) {
    return null;
  }

  $data = file_get_contents($path);
  return $data === false ? null : $data;
}

function normalizeNewlines(string $value): string {
  return str_replace(["\r\n", "\r"], "\n", $value);
}

function stringEndsWith(string $value, string $suffix): bool {
  $suffixLength = strlen($suffix);
  if ($suffixLength === 0) {
    return true;
  }

  return substr($value, -$suffixLength) === $suffix;
}

function trimOneFinalNewline(string $value): string {
  if (stringEndsWith($value, "\r\n")) {
    return substr($value, 0, -2);
  }
  if (stringEndsWith($value, "\n") || stringEndsWith($value, "\r")) {
    return substr($value, 0, -1);
  }
  return $value;
}

function removeControlCharacters(string $value): string {
  return preg_replace('/[[:cntrl:]]/', '', $value) ?? $value;
}

function removeNulBytes(string $value): string {
  return str_replace("\0", '', $value);
}

function htmlDecode(string $value): string {
  return html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function firstDifferenceOffset(string $left, string $right): int {
  $limit = min(strlen($left), strlen($right));
  for ($i = 0; $i < $limit; $i++) {
    if ($left[$i] !== $right[$i]) {
      return $i;
    }
  }
  return strlen($left) === strlen($right) ? -1 : $limit;
}

function sampleAround(string $value, int $offset, int $sampleBytes): string {
  if ($offset < 0) {
    return '';
  }
  $start = max(0, $offset - intdiv($sampleBytes, 2));
  return substr($value, $start, $sampleBytes);
}

function printableSample(string $value): string {
  $escaped = '';
  $length = strlen($value);
  for ($i = 0; $i < $length; $i++) {
    $ord = ord($value[$i]);
    if ($ord === 9) {
      $escaped .= '\\t';
    } elseif ($ord === 10) {
      $escaped .= '\\n';
    } elseif ($ord === 13) {
      $escaped .= '\\r';
    } elseif ($ord < 32 || $ord === 127) {
      $escaped .= sprintf('\\x%02X', $ord);
    } else {
      $escaped .= $value[$i];
    }
  }
  return $escaped;
}

function compareData(?string $sqlData, ?string $diskData): array {
  $sqlHasData = $sqlData !== null && $sqlData !== '';
  $diskHasData = $diskData !== null && $diskData !== '';

  $sqlHash = $sqlHasData ? hash('sha256', $sqlData) : '';
  $diskHash = $diskHasData ? hash('sha256', $diskData) : '';
  $sqlBytes = $sqlHasData ? strlen($sqlData) : 0;
  $diskBytes = $diskHasData ? strlen($diskData) : 0;

  if (!$sqlHasData && !$diskHasData) {
    return ['classification' => 'no_sequence', 'recommended_source' => 'none', 'confidence' => 'high'] + metricDefaults($sqlBytes, $diskBytes, $sqlHash, $diskHash);
  }
  if ($sqlHasData && !$diskHasData) {
    return ['classification' => 'sql_only', 'recommended_source' => 'sql_export_needed', 'confidence' => 'medium'] + metricDefaults($sqlBytes, $diskBytes, $sqlHash, $diskHash);
  }
  if (!$sqlHasData && $diskHasData) {
    return ['classification' => 'disk_only', 'recommended_source' => 'disk', 'confidence' => 'high'] + metricDefaults($sqlBytes, $diskBytes, $sqlHash, $diskHash);
  }
  if ($sqlHash === $diskHash) {
    return ['classification' => 'exact_match', 'recommended_source' => 'either', 'confidence' => 'high'] + metricDefaults($sqlBytes, $diskBytes, $sqlHash, $diskHash);
  }

  $tests = [
    'newline_only' => [normalizeNewlines($sqlData), normalizeNewlines($diskData), 'either_after_newline_normalization', 'high'],
    'one_final_newline_only' => [trimOneFinalNewline($sqlData), trimOneFinalNewline($diskData), 'either_after_final_newline_trim', 'high'],
    'newlines_and_final_newline_only' => [trimOneFinalNewline(normalizeNewlines($sqlData)), trimOneFinalNewline(normalizeNewlines($diskData)), 'either_after_newline_normalization', 'high'],
    'sql_html_entity_encoded' => [htmlDecode($sqlData), $diskData, 'disk', 'high'],
    'disk_html_entity_encoded' => [$sqlData, htmlDecode($diskData), 'sql', 'medium'],
    'control_chars_removed_only' => [removeControlCharacters($sqlData), removeControlCharacters($diskData), 'disk_probably_original', 'medium'],
    'nul_bytes_removed_only' => [removeNulBytes($sqlData), removeNulBytes($diskData), 'disk_probably_original', 'medium'],
    'decoded_sql_and_normalized_newlines' => [normalizeNewlines(htmlDecode($sqlData)), normalizeNewlines($diskData), 'disk', 'high'],
    'control_chars_and_newlines_only' => [removeControlCharacters(normalizeNewlines($sqlData)), removeControlCharacters(normalizeNewlines($diskData)), 'disk_probably_original', 'medium'],
  ];

  foreach ($tests as $classification => [$left, $right, $recommendedSource, $confidence]) {
    if ($left === $right) {
      return [
        'classification' => $classification,
        'recommended_source' => $recommendedSource,
        'confidence' => $confidence,
      ] + metricDefaults($sqlBytes, $diskBytes, $sqlHash, $diskHash);
    }
  }

  $offset = firstDifferenceOffset($sqlData, $diskData);
  return [
    'classification' => 'unresolved_content_difference',
    'recommended_source' => 'manual_review',
    'confidence' => 'low',
    'first_difference_offset' => $offset,
  ] + metricDefaults($sqlBytes, $diskBytes, $sqlHash, $diskHash);
}

function metricDefaults(int $sqlBytes, int $diskBytes, string $sqlHash, string $diskHash): array {
  return [
    'sql_bytes' => $sqlBytes,
    'disk_bytes' => $diskBytes,
    'byte_delta_sql_minus_disk' => $sqlBytes - $diskBytes,
    'sql_sha256' => $sqlHash,
    'disk_sha256' => $diskHash,
    'first_difference_offset' => '',
  ];
}

function safeRecoveryFilename(string $entityType, string $id, string $name, string $originalFilename): string {
  $base = $originalFilename !== '' ? $originalFilename : "$entityType-$id-$name.sequence";
  $base = preg_replace('/[^A-Za-z0-9._-]+/', '_', $base) ?? $base;
  $base = trim($base, '._-');
  if ($base === '') {
    $base = "$entityType-$id.sequence";
  }
  return "$entityType-$id-sql-recovered-$base";
}

function auditTable(PDO $db, string $sequenceDirectory, array $config, $detailsHandle, $recoveryHandle, array &$summary, int $sampleBytes): void {
  $sql = "SELECT {$config['id_column']} AS entity_id, {$config['name_column']} AS entity_name, sequenceDataName_col, sequence_data_col FROM {$config['table']} ORDER BY {$config['id_column']}";
  $statement = $db->prepare($sql);
  $statement->execute();

  while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $entityType = $config['entity_type'];
    $id = (string)$row['entity_id'];
    $name = (string)$row['entity_name'];
    $filename = cleanFilename($row['sequenceDataName_col'] ?? '');
    $sqlData = $row['sequence_data_col'] ?? null;
    $diskData = readDiskData($sequenceDirectory, $filename);
    $comparison = compareData($sqlData, $diskData);
    $classification = $comparison['classification'];
    $summaryKey = "$entityType\t$classification\t{$comparison['recommended_source']}\t{$comparison['confidence']}";
    $summary[$summaryKey] = ($summary[$summaryKey] ?? 0) + 1;

    $offset = $comparison['first_difference_offset'];
    $sqlSample = ($sqlData !== null && $offset !== '') ? sampleAround($sqlData, (int)$offset, $sampleBytes) : '';
    $diskSample = ($diskData !== null && $offset !== '') ? sampleAround($diskData, (int)$offset, $sampleBytes) : '';

    writeTsvRow($detailsHandle, [
      $entityType,
      $id,
      $name,
      $filename,
      $comparison['classification'],
      $comparison['recommended_source'],
      $comparison['confidence'],
      $comparison['sql_bytes'],
      $comparison['disk_bytes'],
      $comparison['byte_delta_sql_minus_disk'],
      $comparison['sql_sha256'],
      $comparison['disk_sha256'],
      $offset,
      bin2hex($sqlSample),
      bin2hex($diskSample),
      printableSample($sqlSample),
      printableSample($diskSample),
    ]);

    if ($classification === 'sql_only') {
      writeTsvRow($recoveryHandle, [
        $entityType,
        $id,
        $name,
        $filename,
        safeRecoveryFilename($entityType, $id, $name, $filename),
        $comparison['sql_bytes'],
        $comparison['sql_sha256'],
      ]);
    }
  }
}

$db = connectDatabase($databaseHost, $databaseName, $databaseUser);
$detailsPath = rtrim($outputDirectory, '/') . '/sequence_difference_details.tsv';
$summaryPath = rtrim($outputDirectory, '/') . '/sequence_difference_summary.tsv';
$recoveryPath = rtrim($outputDirectory, '/') . '/sequence_sql_only_recovery_manifest.tsv';

$detailsHandle = fopen($detailsPath, 'w');
$recoveryHandle = fopen($recoveryPath, 'w');
if ($detailsHandle === false || $recoveryHandle === false) {
  throw new RuntimeException('Could not open output files for writing.');
}

writeTsvRow($detailsHandle, [
  'entity_type',
  'id',
  'name',
  'sequence_filename',
  'classification',
  'recommended_source',
  'confidence',
  'sql_bytes',
  'disk_bytes',
  'byte_delta_sql_minus_disk',
  'sql_sha256',
  'disk_sha256',
  'first_difference_offset',
  'sql_sample_hex',
  'disk_sample_hex',
  'sql_sample_printable',
  'disk_sample_printable',
]);

writeTsvRow($recoveryHandle, [
  'entity_type',
  'id',
  'name',
  'sequence_filename',
  'suggested_recovery_filename',
  'sql_bytes',
  'sql_sha256',
]);

$summary = [];
auditTable($db, $sequenceDirectory, [
  'entity_type' => 'allele',
  'table' => 'allele_table',
  'id_column' => 'allele_id',
  'name_column' => 'alleleName_col',
], $detailsHandle, $recoveryHandle, $summary, $sampleBytes);

auditTable($db, $sequenceDirectory, [
  'entity_type' => 'plasmid',
  'table' => 'plasmid_table',
  'id_column' => 'plasmid_id',
  'name_column' => 'plasmidName_col',
], $detailsHandle, $recoveryHandle, $summary, $sampleBytes);

fclose($detailsHandle);
fclose($recoveryHandle);

$summaryHandle = fopen($summaryPath, 'w');
if ($summaryHandle === false) {
  throw new RuntimeException("Could not write $summaryPath");
}
writeTsvRow($summaryHandle, ['entity_type', 'classification', 'recommended_source', 'confidence', 'count']);
ksort($summary);
foreach ($summary as $summaryKey => $count) {
  writeTsvRow($summaryHandle, array_merge(explode("\t", $summaryKey), [$count]));
}
fclose($summaryHandle);

echo "Wrote $detailsPath\n";
echo "Wrote $summaryPath\n";
echo "Wrote $recoveryPath\n";
