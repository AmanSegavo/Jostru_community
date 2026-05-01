<?php
try {
    $conn = new PDO('mysql:host=sql100.byetcluster.com;port=3306;dbname=if0_41649436_jostru', 'if0_41649436', 'djafu12345', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]);
    echo 'Remote DB Connected! HOST: sql100.byetcluster.com<br>';
} catch (Exception $e) {
    echo 'Remote DB Error (sql100.byetcluster.com): ' . $e->getMessage() . '<br>';
}

try {
    $conn2 = new PDO('mysql:host=sql100.infinityfree.com;port=3306;dbname=if0_41649436_jostru', 'if0_41649436', 'djafu12345', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]);
    echo 'Remote DB Connected! HOST: sql100.infinityfree.com<br>';
} catch (Exception $e) {
    echo 'Remote DB Error (sql100.infinityfree.com): ' . $e->getMessage() . '<br>';
}

try {
    $conn3 = new PDO('mysql:host=sql100.epizy.com;port=3306;dbname=if0_41649436_jostru', 'if0_41649436', 'djafu12345', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]);
    echo 'Remote DB Connected! HOST: sql100.epizy.com<br>';
} catch (Exception $e) {
    echo 'Remote DB Error (sql100.epizy.com): ' . $e->getMessage() . '<br>';
}
