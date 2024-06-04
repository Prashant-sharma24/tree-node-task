<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $parentId = $_POST['parent'];
    $createdDate = date('Y-m-d H:i:s');

    try {
        $pdo = new PDO('mysql:host=localhost;dbname=john_doe', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("INSERT INTO Members (CreatedDate, Name, ParentId) VALUES (?, ?, ?)");
        $stmt->execute([$createdDate, $name, $parentId]);
        $lastId = $pdo->lastInsertId();
        echo json_encode(['Id' => $lastId, 'Name' => $name, 'ParentId' => $parentId]);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
}
?>
