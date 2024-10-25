<?php
// fetch_emails.php
include 'db.php';

header('Content-Type: application/json');

$stmt = $pdo->prepare("SELECT email, fname FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($users); // Send users as JSON data to the front end
?>
