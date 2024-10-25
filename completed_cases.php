<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch completed cases
$stmt = $pdo->prepare("SELECT * FROM criminal_cases WHERE user_id = ? AND progress = 'Completed'");
$stmt->execute([$userId]);
$completedCases = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Completed Cases</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Completed Cases</h1>
        <a href="dashboard.php" class="btn btn-primary mb-3">Back to Dashboard</a>
        <table class="table">
            <thead>
                <tr>
                    <th>Case Entry</th>
                    <th>Description</th>
                    <th>Priority Status</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($completedCases)): ?>
                    <tr>
                        <td colspan="4">No completed cases found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($completedCases as $case): ?>
                        <tr>
                            <td><?= htmlspecialchars($case['case_type']) ?></td>
                            <td><?= htmlspecialchars($case['description']) ?></td>
                            <td><?= htmlspecialchars($case['case_priority']) ?></td>
                            <td><?= htmlspecialchars($case['progress']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
