<?php
function getCoordinatesFromGoogleMapsLink($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    echo "Final URL: " . $finalUrl . "\n";
    if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $finalUrl, $matches)) {
        return ['lat' => $matches[1], 'lng' => $matches[2]];
    }
    
    // Also check if coordinates are in a different format like ?q=-2.123,104.123
    if (preg_match('/(?:\?|&)q=(-?\d+\.\d+),(-?\d+\.\d+)/', $finalUrl, $matches)) {
        return ['lat' => $matches[1], 'lng' => $matches[2]];
    }

    return null;
}

$url = "https://maps.app.goo.gl/E4yXZe35mZ5eE2Y89"; // random example if possible, or just print logic
echo "Testing...\n";
