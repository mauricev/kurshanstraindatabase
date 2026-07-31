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
Usage: php scripts/classify_missing_sequence_sql_risk.php [options]

Read-only report that uses records with both SQL and disk sequence data to
estimate whether missing disk files still have a trustworthy SQL copy.

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
  missing_sequence_sql_risk.tsv
  sequence_sql_risk_training_summary.tsv

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
    $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    if (defined('Pdo\Mysql::ATTR_USE_BUFFERED_QUERY')) {
      $options[constant('Pdo\Mysql::ATTR_USE_BUFFERED_QUERY')] = false;
    } elseif (defined('PDO::MYSQL_ATTR_USE_BUFFERED_QUERY')) {
      $options[@constant('PDO::MYSQL_ATTR_USE_BUFFERED_QUERY')] = false;
    }

    return new PDO($dsn, $user, $password, $options);
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

function readDiskData(string $sequenceDirectory, string $filename): ?string {
  if ($filename === '' || !isSafeSequenceFilename($filename)) {
    return null;
  }

  $path = sequencePath($sequenceDirectory, $filename);
  if (!is_file($path) || !is_readable($path)) {
    return null;
  }

  $data = file_get_contents($path);
  return $data === false ? null : $data;
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

function filenameExtension(string $filename): string {
  $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
  return $extension === '' ? '(none)' : $extension;
}

function stringEndsWith(string $value, string $suffix): bool {
  $suffixLength = strlen($suffix);
  if ($suffixLength === 0) {
    return true;
  }

  return substr($value, -$suffixLength) === $suffix;
}

function normalizeNewlines(string $value): string {
  return str_replace(["\r\n", "\r"], "\n", $value);
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

function compareForTraining(string $sqlData, string $diskData): string {
  if (hash('sha256', $sqlData) === hash('sha256', $diskData)) {
    return 'exact_match';
  }

  if (normalizeNewlines($sqlData) === normalizeNewlines($diskData)) {
    return 'newline_only';
  }

  if (trimOneFinalNewline($sqlData) === trimOneFinalNewline($diskData)) {
    return 'one_final_newline_only';
  }

  $sqlNormalized = trimOneFinalNewline(normalizeNewlines($sqlData));
  $diskNormalized = trimOneFinalNewline(normalizeNewlines($diskData));
  if ($sqlNormalized === $diskNormalized) {
    return 'newlines_and_final_newline_only';
  }

  return 'unresolved_content_difference';
}

function initialStats(): array {
  return [
    'total_compared' => 0,
    'exact_match' => 0,
    'benign_difference' => 0,
    'unresolved_content_difference' => 0,
    'sql_larger' => 0,
    'disk_larger' => 0,
    'same_size' => 0,
    'sql_binary_like' => 0,
    'disk_binary_like' => 0,
  ];
}

function isBinaryLike(string $data): bool {
  if (strpos($data, "\0") !== false) {
    return true;
  }

  $sample = substr($data, 0, 4096);
  $length = strlen($sample);
  if ($length === 0) {
    return false;
  }

  $controlCount = 0;
  for ($i = 0; $i < $length; $i++) {
    $ord = ord($sample[$i]);
    if ($ord < 32 && $ord !== 9 && $ord !== 10 && $ord !== 13) {
      $controlCount++;
    }
  }

  return ($controlCount / $length) > 0.02;
}

function addTrainingStats(array &$statsByExtension, string $extension, string $classification, string $sqlData, string $diskData): void {
  if (!isset($statsByExtension[$extension])) {
    $statsByExtension[$extension] = initialStats();
  }

  $statsByExtension[$extension]['total_compared']++;
  if ($classification === 'exact_match') {
    $statsByExtension[$extension]['exact_match']++;
  } elseif ($classification === 'unresolved_content_difference') {
    $statsByExtension[$extension]['unresolved_content_difference']++;
  } else {
    $statsByExtension[$extension]['benign_difference']++;
  }

  $sqlBytes = strlen($sqlData);
  $diskBytes = strlen($diskData);
  if ($sqlBytes > $diskBytes) {
    $statsByExtension[$extension]['sql_larger']++;
  } elseif ($diskBytes > $sqlBytes) {
    $statsByExtension[$extension]['disk_larger']++;
  } else {
    $statsByExtension[$extension]['same_size']++;
  }

  if (isBinaryLike($sqlData)) {
    $statsByExtension[$extension]['sql_binary_like']++;
  }
  if (isBinaryLike($diskData)) {
    $statsByExtension[$extension]['disk_binary_like']++;
  }
}

function sqlClues(?string $sqlData): array {
  if ($sqlData === null || $sqlData === '') {
    return [
      'has_sql_data' => false,
      'sql_bytes' => 0,
      'sql_sha256' => '',
      'sql_binary_like' => false,
      'sql_contains_snapgene' => false,
    ];
  }

  return [
    'has_sql_data' => true,
    'sql_bytes' => strlen($sqlData),
    'sql_sha256' => hash('sha256', $sqlData),
    'sql_binary_like' => isBinaryLike($sqlData),
    'sql_contains_snapgene' => strpos(substr($sqlData, 0, 256), 'SnapGene') !== false,
  ];
}

function extensionFamilyRisk(string $extension): string {
  $highRiskBinaryExtensions = ['ab1', 'abi', 'dna', 'scf'];
  $textExtensions = ['fa', 'fas', 'fasta', 'gb', 'gbk', 'gp', 'seq', 'txt'];

  if (in_array($extension, $highRiskBinaryExtensions, true)) {
    return 'high';
  }
  if (in_array($extension, $textExtensions, true)) {
    return 'low';
  }
  if ($extension === 'ape') {
    return 'medium';
  }

  return 'unknown';
}

function classifyMissingSqlCopy(string $extension, array $statsByExtension, array $clues): array {
  if (!$clues['has_sql_data']) {
    return [
      'risk' => 'no_sql_copy',
      'rationale' => 'Disk file is missing and sequence_data_col is empty; there is no SQL copy to evaluate.',
    ];
  }

  $stats = $statsByExtension[$extension] ?? null;
  $familyRisk = extensionFamilyRisk($extension);
  $parts = [];
  $risk = 'unknown_needs_manual_review';

  if ($stats !== null && $stats['total_compared'] > 0) {
    $total = $stats['total_compared'];
    $unresolved = $stats['unresolved_content_difference'];
    $benign = $stats['exact_match'] + $stats['benign_difference'];
    $unresolvedPercent = round(($unresolved / $total) * 100);
    $benignPercent = round(($benign / $total) * 100);
    $parts[] = ".$extension training: $unresolved of $total ($unresolvedPercent%) comparable SQL/disk rows had unresolved content differences; $benignPercent% were exact or newline-only.";

    if ($unresolvedPercent >= 50) {
      $risk = 'high_probably_corrupt';
    } elseif ($unresolvedPercent > 0) {
      $risk = $familyRisk === 'high' ? 'high_probably_corrupt' : 'medium_needs_validation';
    } elseif ($benignPercent >= 90) {
      $risk = 'low_likely_recoverable';
    } else {
      $risk = 'medium_needs_validation';
    }

    if ($stats['sql_larger'] > 0 && $unresolved > 0) {
      $parts[] = "In differing .$extension examples, SQL was often larger than disk, consistent with text/binary expansion during upload.";
    }
  } else {
    $parts[] = "No comparable .$extension SQL/disk rows were available for local training.";
    if ($familyRisk === 'high') {
      $risk = 'high_probably_corrupt';
    } elseif ($familyRisk === 'low') {
      $risk = 'medium_needs_validation';
    }
  }

  if ($familyRisk === 'high' && ($clues['sql_binary_like'] || $clues['sql_contains_snapgene'])) {
    $risk = 'high_probably_corrupt';
    $parts[] = 'The SQL copy looks binary-like, which is exactly the case the old SQL storage path handled poorly.';
  } elseif ($familyRisk === 'low' && !$clues['sql_binary_like'] && $risk === 'unknown_needs_manual_review') {
    $risk = 'medium_needs_validation';
    $parts[] = 'The extension is usually text-like and the SQL copy is not binary-looking, but no disk original remains for a byte-for-byte check.';
  } elseif ($clues['sql_binary_like']) {
    $risk = $risk === 'low_likely_recoverable' ? 'medium_needs_validation' : $risk;
    $parts[] = 'The SQL copy contains binary/control-byte patterns.';
  }

  return [
    'risk' => $risk,
    'rationale' => implode(' ', $parts),
  ];
}

function collectTable(PDO $db, string $sequenceDirectory, array $config, array &$statsByExtension, array &$missingRows): void {
  $sql = "SELECT {$config['id_column']} AS entity_id, {$config['name_column']} AS entity_name, sequenceDataName_col, sequence_data_col FROM {$config['table']} WHERE sequenceDataName_col IS NOT NULL AND sequenceDataName_col != '' ORDER BY {$config['id_column']}";
  $statement = $db->prepare($sql);
  $statement->execute();

  while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $filename = cleanFilename($row['sequenceDataName_col'] ?? '');
    $extension = filenameExtension($filename);
    $sqlData = $row['sequence_data_col'] ?? null;
    $hasSqlData = $sqlData !== null && $sqlData !== '';
    $reason = missingReason($sequenceDirectory, $filename);

    if ($reason !== '') {
      $missingRows[] = [
        'entity_type' => $config['entity_type'],
        'id' => (string)$row['entity_id'],
        'name' => (string)$row['entity_name'],
        'sequence_filename' => $filename,
        'extension' => $extension,
        'missing_reason' => $reason,
        'expected_path' => isSafeSequenceFilename($filename) ? sequencePath($sequenceDirectory, $filename) : '',
        'sql_clues' => sqlClues($sqlData),
      ];
      continue;
    }

    if (!$hasSqlData) {
      continue;
    }

    $diskData = readDiskData($sequenceDirectory, $filename);
    if ($diskData === null || $diskData === '') {
      continue;
    }

    addTrainingStats(
      $statsByExtension,
      $extension,
      compareForTraining($sqlData, $diskData),
      $sqlData,
      $diskData
    );
  }
}

$db = connectDatabase($databaseHost, $databaseName, $databaseUser);
$statsByExtension = [];
$missingRows = [];

collectTable($db, $sequenceDirectory, [
  'entity_type' => 'allele',
  'table' => 'allele_table',
  'id_column' => 'allele_id',
  'name_column' => 'alleleName_col',
], $statsByExtension, $missingRows);

collectTable($db, $sequenceDirectory, [
  'entity_type' => 'plasmid',
  'table' => 'plasmid_table',
  'id_column' => 'plasmid_id',
  'name_column' => 'plasmidName_col',
], $statsByExtension, $missingRows);

ksort($statsByExtension);

$riskPath = rtrim($outputDirectory, '/') . '/missing_sequence_sql_risk.tsv';
$trainingPath = rtrim($outputDirectory, '/') . '/sequence_sql_risk_training_summary.tsv';
$riskHandle = fopen($riskPath, 'w');
$trainingHandle = fopen($trainingPath, 'w');
if ($riskHandle === false || $trainingHandle === false) {
  throw new RuntimeException('Could not open output files for writing.');
}

writeTsvRow($riskHandle, [
  'entity_type',
  'id',
  'name',
  'sequence_filename',
  'extension',
  'risk',
  'rationale',
  'missing_reason',
  'sql_bytes',
  'sql_sha256',
  'sql_binary_like',
  'sql_contains_snapgene',
]);

foreach ($missingRows as $row) {
  $clues = $row['sql_clues'];
  $classification = classifyMissingSqlCopy($row['extension'], $statsByExtension, $clues);
  writeTsvRow($riskHandle, [
    $row['entity_type'],
    $row['id'],
    $row['name'],
    $row['sequence_filename'],
    $row['extension'],
    $classification['risk'],
    $classification['rationale'],
    $row['missing_reason'],
    $clues['sql_bytes'],
    $clues['sql_sha256'],
    $clues['sql_binary_like'] ? 'yes' : 'no',
    $clues['sql_contains_snapgene'] ? 'yes' : 'no',
  ]);
}

writeTsvRow($trainingHandle, [
  'extension',
  'total_compared',
  'exact_match',
  'benign_difference',
  'unresolved_content_difference',
  'sql_larger',
  'disk_larger',
  'same_size',
  'sql_binary_like',
  'disk_binary_like',
]);

foreach ($statsByExtension as $extension => $stats) {
  writeTsvRow($trainingHandle, [
    $extension,
    $stats['total_compared'],
    $stats['exact_match'],
    $stats['benign_difference'],
    $stats['unresolved_content_difference'],
    $stats['sql_larger'],
    $stats['disk_larger'],
    $stats['same_size'],
    $stats['sql_binary_like'],
    $stats['disk_binary_like'],
  ]);
}

fclose($riskHandle);
fclose($trainingHandle);

echo "Wrote $riskPath\n";
echo "Wrote $trainingPath\n";
