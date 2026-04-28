<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Loading autoload.php...<br>";
try {
    require __DIR__.'/vendor/autoload.php';
    echo "Autoload.php loaded successfully!<br>";
} catch (Exception $e) {
    echo "Error loading autoload.php: " . $e->getMessage() . "<br>";
}

echo "Testing Socialite class existence...<br>";
if (class_exists('Laravel\Socialite\SocialiteServiceProvider')) {
    echo "SocialiteServiceProvider found!<br>";
} else {
    echo "SocialiteServiceProvider NOT found.<br>";
}
?>
