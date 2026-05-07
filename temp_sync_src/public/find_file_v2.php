<?php
$path = "/home/u380603901/domains/jostru.site/public_html/public/feed";
if (file_exists($path)) {
    echo "Path exists. Type: " . (is_link($path) ? "LINK" : "DIR") . "\n";
    if (is_link($path)) {
        echo "Link target: " . readlink($path) . "\n";
    }
} else {
    echo "Path does not exist.\n";
}
