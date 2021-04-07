<?php

function force($file) {
  if (file_exists($file)) {
    // reset php output buffer
    if (ob_get_level()) {
      ob_end_clean();
    }
    // save file in browser
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    // read the file and send it to user
    if ($fd = fopen($file, 'rb')) {
      while (!feof($fd)) {
        print fread($fd, 1024);
      }
      fclose($fd);
    }
    exit;
  }
}
// Get real path for our folder
$rootPath = realpath('./');
$dir=basename(__DIR__);

// Initialize archive object
$zip = new ZipArchive();

//remove file if exists
$failo =$dir.'.zip';
if (file_exists($failo)) unlink($failo);

$zip->open($dir.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

// Create recursive directory iterator
/** @var SplFileInfo[] $files */
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file)
{
    // Skip directories (they would be added automatically)
    if (!$file->isDir())
    {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add current file to archive
//remove itself from archive
   if ($relativePath != 'download.php')     $zip->addFile($filePath, $relativePath);
    }
}

// Zip archive will be created only after closing object
$zip->close();

force($dir.".zip");

?>
