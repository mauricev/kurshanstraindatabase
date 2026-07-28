<?php
require_once(__DIR__ . '/../classes/classes_app_settings.php');

function sequenceFilenameFromContentDisposition(?string $contentDisposition): string {
  if ($contentDisposition === NULL) {
    throw new InvalidArgumentException('Missing content disposition.');
  }

  if (!(preg_match('/(?:^|;)\s*filename="?(?<filename>[^";]+)"?/i', $contentDisposition, $matches))) {
    throw new InvalidArgumentException('Missing sequence filename.');
  }

  return sequenceSafeFilename($matches['filename']);
}

function sequenceSafeFilename(?string $filename): string {
  $filename = trim((string)$filename);

  if ($filename === '') {
    throw new InvalidArgumentException('Missing sequence filename.');
  }

  if ($filename !== basename($filename)) {
    throw new InvalidArgumentException('Invalid sequence filename.');
  }

  if (preg_match('/[\/\\\\\x00-\x1F\x7F]/', $filename)) {
    throw new InvalidArgumentException('Invalid sequence filename.');
  }

  if (!(preg_match('/\A[A-Za-z0-9][A-Za-z0-9.,_ -]{0,254}\z/', $filename))) {
    throw new InvalidArgumentException('Invalid sequence filename.');
  }

  return $filename;
}

function sequenceFilePath(string $filename, bool $mustExist): string {
  $sequenceDirectory = AppSettings::sequenceFilesDirectory();
  $realSequenceDirectory = realpath($sequenceDirectory);

  if ($realSequenceDirectory === false || !(is_dir($realSequenceDirectory))) {
    throw new RuntimeException('Sequence files directory is not available.');
  }

  $filename = sequenceSafeFilename($filename);
  $path = $realSequenceDirectory . DIRECTORY_SEPARATOR . $filename;

  if ($mustExist) {
    $realPath = realpath($path);

    if ($realPath === false || !(is_file($realPath))) {
      throw new RuntimeException('Sequence file not found.');
    }

    $directoryPrefix = $realSequenceDirectory . DIRECTORY_SEPARATOR;
    if (strncmp($realPath, $directoryPrefix, strlen($directoryPrefix)) !== 0) {
      throw new RuntimeException('Invalid sequence file path.');
    }

    return $realPath;
  }

  return $path;
}
?>
