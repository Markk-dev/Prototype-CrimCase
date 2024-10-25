<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $caseId = $_POST['case_id'];
    $caseType = $_POST['case_type'];
    $description = $_POST['description'];
    $casePriority = $_POST['case_priority'];
    $progress = $_POST['progress']; // Allow changing progress

    // Validate description length (max 20 characters)
    if (strlen($description) > 20) {
        header('Location: edit_case.php?id=' . $caseId . '&error=' . urlencode("Error: Description must not exceed 20 characters."));
        exit();
    }

    // Escape strings for SQL
    $caseType = $pdo->quote($caseType);
    $description = $pdo->quote($description);
    $casePriority = $pdo->quote($casePriority);
    $progress = $pdo->quote($progress);

    // Update case in the database
    $sql = "UPDATE criminal_cases SET case_type = $caseType, description = $description, case_priority = $casePriority, progress = $progress WHERE id = $caseId";
    
    if ($pdo->exec($sql)) {
        header('Location: dashboard.php');
        exit();
    } else {
        header('Location: edit_case.php?id=' . $caseId . '&error=' . urlencode("Error: Unable to update case. Please try again."));
        exit();
    }
}

// Fetch the case details for editing
$caseId = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM criminal_cases WHERE id = ?");
$stmt->execute([$caseId]);
$case = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Case</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Edit Case</h1>
        <form method="POST" action="edit_case.php">
            <input type="hidden" name="case_id" value="<?= htmlspecialchars($case['id']) ?>">
            <div class="form-group">
                <label for="case_type">Case</label>
                <input type="text" name="case_type" class="form-control" value="<?= htmlspecialchars($case['case_type']) ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description (max 20 characters)</label>
                <textarea name="description" class="form-control" required maxlength="20"><?= htmlspecialchars($case['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="case_priority">Priority</label>
                <select name="case_priority" class="form-control">
                    <option value="High" <?= $case['case_priority'] == 'High' ? 'selected' : '' ?>>High</option>
                    <option value="Medium" <?= $case['case_priority'] == 'Medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="Low" <?= $case['case_priority'] == 'Low' ? 'selected' : '' ?>>Low</option>
                </select>
            </div>
            <div class="form-group">
                <label for="progress">Status</label>
                <select name="progress" class="form-control">
                    <option value="Pending" <?= $case['progress'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Ongoing" <?= $case['progress'] == 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
                    <option value="Completed" <?= $case['progress'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Case</button>
        </form>
    </div>
</body>
</html>
