<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Loading autoload.php...<br>";
require __DIR__.'/vendor/autoload.php';

echo "Testing Database Connection (REAL CREDENTIALS)...<br>";
try {
    $host = 'sql100.byetcluster.com';
    $db   = 'if0_41649436_jostru';
    $user = 'if0_41649436';
    $pass = 'djafu12345';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Database connected successfully!<br>";
    
    $stmt = $pdo->query('SELECT count(*) FROM users');
    echo "Users count: " . $stmt->fetchColumn() . "<br>";

} catch (\PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "<br>";
}
?>
