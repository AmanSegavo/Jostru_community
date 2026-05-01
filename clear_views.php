<?php
\ = __DIR__ . '/../storage/framework/views';
if (is_dir(\)) {
    \ = glob(\ . '/*');
    foreach (\ as \) {
        if (is_file(\)) {
            unlink(\);
        }
    }
    echo "Views cache cleared (" . count(\) . " files).";
}
