<?php
if (class_exists('ZipArchive')) {
    echo "ZipArchive exists!<br>";
    if (isset($_GET['unzip'])) {
        $zip = new ZipArchive;
        if ($zip->open('vendor.zip') === TRUE) {
            $zip->extractTo('.');
            $zip->close();
            echo "Successfully unzipped vendor.zip!";
        } else {
            echo "Failed to open vendor.zip";
        }
    }
} else {
    echo "ZipArchive NOT found";
}
?>
<br><a href="?unzip=1">Run Unzip</a>
