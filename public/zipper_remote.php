<?php
$zip = new ZipArchive();
$filename = "backup_remote_src.zip";

if ($zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE)!==TRUE) {
    exit("cannot open <$filename>\n");
}

$dir = dirname(__DIR__); // public_html

$allowedDirs = ['app', 'resources', 'routes', 'config', 'database', 'public'];

foreach ($allowedDirs as $target) {
    $targetDir = $dir . DIRECTORY_SEPARATOR . $target;
    if (!is_dir($targetDir)) continue;

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($targetDir, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if (!$file->isDir()) {
            $realPath = $file->getRealPath();
            $relativePath = substr($realPath, strlen(realpath($dir)) + 1);
            
            // Skip zip file itself
            if (strpos($relativePath, 'backup_remote') !== false) continue;
            
            $zip->addFile($realPath, str_replace('\\', '/', $relativePath));
        }
    }
}

// Add root files
$rootFiles = ['composer.json', 'package.json', '.env', 'server.php', 'vite.config.js'];
foreach ($rootFiles as $rf) {
    if (file_exists($dir . DIRECTORY_SEPARATOR . $rf)) {
        $zip->addFile($dir . DIRECTORY_SEPARATOR . $rf, $rf);
    }
}

$zip->close();
echo "Zip created successfully: <a href='backup_remote_src.zip'>Download</a>";
?>
