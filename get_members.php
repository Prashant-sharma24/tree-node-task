<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=john_doe', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT * FROM Members");
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($members as $member) {
        echo "<option value='{$member['Id']}'>{$member['Name']}</option>";
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
