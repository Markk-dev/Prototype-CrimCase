<?php
session_start();
include 'db.php';
include 'fetch_emails.php'; // Include the email sending functionality

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo "Unauthorized";
        exit();
    }

    $caseType = $_POST['case_type'];
    $description = $_POST['description'];
    $casePriority = $_POST['case_priority'];
    $userId = $_SESSION['user_id'];

    if (strlen($description) > 20) {
        http_response_code(400);
        echo "Description must not exceed 20 characters.";
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO criminal_cases (case_type, description, case_priority, progress, user_id) VALUES (?, ?, ?, 'Pending', ?)");
        $stmt->execute([$caseType, $description, $casePriority, $userId]);
        
        // Call function to send notifications
        sendCaseNotification($caseType, $description, $casePriority); 

        http_response_code(200);
        echo "Case added successfully";
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Failed to add case: " . $e->getMessage();
    }
}

function sendCaseNotification($caseName, $description, $priority) {
    global $pdo;

    try {
        $userStmt = $pdo->prepare("SELECT email, fname FROM users");
        $userStmt->execute();
        $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($users as $user) {
            $templateParams = [
                'to_email' => $user['email'],
                'user_name' => $user['fname'],
                'case_title' => $caseName,
                'case_description' => $description,
                'priority_status' => $priority,
                'creator_name' => 'CaseFlow'
            ];

            $emailJsUrl = "https://api.emailjs.com/api/v1.0/email/send";
            $postData = json_encode([
                'service_id' => 'service_rv6anec',
                'template_id' => 'template_j23kdbq',
                'user_id' => 'QHVwy3-idEAd_3sVk',
                'template_params' => $templateParams
            ]);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $emailJsUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                echo 'Error sending to ' . $user['email'] . ': ' . curl_error($ch) . "<br>";
            } else {
                echo "Email sent to " . $user['email'] . "<br>";
            }

            curl_close($ch);
        }
    } catch (Exception $e) {
        echo "Failed to send notification emails: " . $e->getMessage() . "<br>";
    }
}
?>
