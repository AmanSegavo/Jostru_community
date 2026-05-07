<?php
$zip = new ZipArchive();
$filename = "backup_remote.zip";

if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
    exit("cannot open <$filename>\n");
}

$dir = '.';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($iterator as $key=>$value) {
    if (!$value->isDir()) {
        $realPath = $value->getRealPath();
        $relativePath = substr($realPath, strlen(realpath($dir)) + 1);
        
        // Skip some directories that are large and don't change often
        if (strpos($relativePath, 'vendor\\') === 0 || strpos($relativePath, 'vendor/') === 0) continue;
        if (strpos($relativePath, 'node_modules\\') === 0 || strpos($relativePath, 'node_modules/') === 0) continue;
        if (strpos($relativePath, 'storage\\framework\\') === 0 || strpos($relativePath, 'storage/framework/') === 0) continue;
        if ($relativePath === 'backup_remote.zip') continue;

        $zip->addFile($realPath, $relativePath);
    }
}
$zip->close();
echo "Zip created successfully: <a href='backup_remote.zip'>Download</a>";
?>
