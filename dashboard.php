<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch all cases and sort by priority
$stmt = $pdo->prepare("SELECT * FROM criminal_cases WHERE progress != 'Completed' ORDER BY 
    CASE 
        WHEN case_priority = 'High' THEN 1
        WHEN case_priority = 'Medium' THEN 2
        WHEN case_priority = 'Low' THEN 3
    END");
$stmt->execute();
$cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Dashboard</h1>
        <a href="logout.php" class="btn btn-danger">Logout</a>
        <h2 class="mt-4">Cases</h2>
        <a href="add_case.php" class="btn btn-primary mb-3">Add New Case</a>
        <a href="completed_cases.php" class="btn btn-secondary mb-3">View Completed Cases</a>
        <table class="table">
            <thead>
                <tr>
                    <th>Case Entry</th>
                    <th>Description</th>
                    <th>Priority Status</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cases)): ?>
                    <tr>
                        <td colspan="5">No cases found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cases as $case): ?>
                        <tr>
                            <td><?= htmlspecialchars($case['case_type']) ?></td>
                            <td><?= htmlspecialchars($case['description']) ?></td>
                            <td><?= htmlspecialchars($case['case_priority']) ?></td>
                            <td><?= htmlspecialchars($case['progress']) ?></td>
                            <td>
                                <a href="edit_case.php?id=<?= $case['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_case.php?id=<?= $case['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                                <a href="mark_done.php?id=<?= $case['id'] ?>" class="btn btn-success btn-sm">Mark as Done</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
