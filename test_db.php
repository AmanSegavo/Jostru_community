<?php
try {
    $conn = new PDO('mysql:host=sql100.infinityfree.com;port=3306;dbname=if0_41649436_jostru', 'if0_41649436', 'djafu12345', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]);
    echo 'Remote DB Connected!';
} catch (Exception $e) {
    echo 'Remote DB Error: ' . $e->getMessage();
}
