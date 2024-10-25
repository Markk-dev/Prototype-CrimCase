<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $caseId = $_GET['id'];
    $stmt = $pdo->prepare("UPDATE criminal_cases SET progress = 'Completed' WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$caseId, $_SESSION['user_id']])) {
        header('Location: dashboard.php');
        exit();
    } else {
        header('Location: dashboard.php?error=' . urlencode("Error: Unable to mark case as done."));
        exit();
    }
}
?>
