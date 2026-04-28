<?php
try {
    $pdo = new PDO("mysql:host=sql100.byetcluster.com;dbname=if0_41649436_jostru", "if0_41649436", "djafu12345");
    $stmt = $pdo->prepare("SELECT name, email, role FROM users WHERE email = ?");
    $stmt->execute(["plikocommunity@gmail.com"]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        echo "User found: " . json_encode($user);
    } else {
        echo "User NOT found";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
