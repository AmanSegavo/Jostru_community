<?php
try {
    $pdo = new PDO("mysql:host=sql100.byetcluster.com;dbname=if0_41649436_jostru", "if0_41649436", "djafu12345");
    $stmt = $pdo->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->execute(["plikocommunity@gmail.com"]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $check = password_verify('djafu12345@#', $user['password']);
        echo "Password check for djafu12345@#: " . ($check ? "CORRECT" : "WRONG");
        echo "<br>Hash: " . $user['password'];
    } else {
        echo "User not found";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
