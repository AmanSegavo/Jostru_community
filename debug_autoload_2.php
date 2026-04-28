<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Loading autoload.php...<br>";
require __DIR__.'/vendor/autoload.php';

echo "Testing GuzzleHttp\Client...<br>";
if (class_exists('GuzzleHttp\Client')) {
    echo "GuzzleHttp\Client found!<br>";
} else {
    echo "GuzzleHttp\Client NOT found.<br>";
}

echo "Testing Socialite...<br>";
if (class_exists('Laravel\Socialite\SocialiteServiceProvider')) {
    echo "SocialiteServiceProvider found!<br>";
} else {
    echo "SocialiteServiceProvider NOT found.<br>";
}

echo "Testing League\OAuth1\Client\Server\Server...<br>";
if (class_exists('League\OAuth1\Client\Server\Server')) {
    echo "OAuth1 Server found!<br>";
} else {
    echo "OAuth1 Server NOT found.<br>";
}
?>
