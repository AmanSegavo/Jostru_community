<?php
$host = '127.0.0.1';
$db   = 'u380603901_Jostru';
$user = 'u380603901_Jostru';
$pass = 'Lk412119';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $cols = $pdo->query("SHOW COLUMNS FROM posts")->fetchAll(PDO::FETCH_COLUMN);
    
    $sqls = [];
    if (!in_array('link_url', $cols)) $sqls[] = "ALTER TABLE posts ADD COLUMN link_url VARCHAR(500) NULL AFTER file_size";
    if (!in_array('tags', $cols))     $sqls[] = "ALTER TABLE posts ADD COLUMN tags VARCHAR(255) NULL AFTER link_url";
    if (!in_array('pinned', $cols))   $sqls[] = "ALTER TABLE posts ADD COLUMN pinned TINYINT(1) DEFAULT 0 AFTER tags";

    foreach ($sqls as $sql) {
        $pdo->exec($sql);
        echo "OK: $sql<br>";
    }
    
    if (empty($sqls)) echo "All columns already exist - no changes needed.";
    
    // Also ensure public/feed exists
    $feedDir = __DIR__ . '/feed';
    if (!is_dir($feedDir)) { mkdir($feedDir, 0777, true); echo "<br>Created feed dir"; }
    else { chmod($feedDir, 0777); echo "<br>feed dir OK"; }

    echo "<br><strong>Done!</strong>";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>