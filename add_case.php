<?php
session_start();
include 'db.php';
include 'fetch_emails.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Case</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Add New Case</h1>
        <form method="POST" action="process_case.php" id="addCaseForm">
            <div class="form-group">
                <label for="case_type">Case</label>
                <input type="text" name="case_type" class="form-control" required placeholder="Case Type">
            </div>
            <div class="form-group">
                <label for="description">Description (max 20 characters)</label>
                <textarea name="description" class="form-control" required placeholder="Description" maxlength="20"></textarea>
            </div>
            <div class="form-group">
                <label for="case_priority">Priority</label>
                <select name="case_priority" class="form-control">
                    <option value="High">High</option>
                    <option value="Medium">Medium</option>
                    <option value="Low">Low</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Case</button>
        </form>
    </div>

    <script>
        // Handle the form submission
        document.getElementById("addCaseForm").addEventListener("submit", async function(event) {
            event.preventDefault();

            // Gather form data
            const formData = new FormData(this);

            // Submit case data to the process_case.php
            const response = await fetch("process_case.php", {
                method: "POST",
                body: formData
            });

            if (response.ok) {
                alert("Case added successfully. Sending notification emails...");

                // Send notifications after the case is added successfully
                sendNotifications();
            } else {
                alert("Failed to add case.");
            }
        });

        // Function to send notification emails via EmailJS
        // Function to send notification emails via EmailJS
async function sendNotifications() {
    try {
        const caseName = document.querySelector('input[name="case_type"]').value;
        const description = document.querySelector('textarea[name="description"]').value;
        const priority = document.querySelector('select[name="case_priority"]').value;

        // Fetch emails from server
        const { data: users } = await axios.get("fetch_emails.php");

        // Loop through each user to send an email
        for (const user of users) {
            const templateParams = {
                to_email: user.email,
                user_name: user.fname,            // For [User’s Name]
                case_title: caseName,              // For [Case Title]
                case_description: description,     // For [Brief description of the case]
                priority_status: priority,         // For [Priority level]
                creator_name: 'CaseFlow'           // Replace as needed
            };

            await axios.post("https://api.emailjs.com/api/v1.0/email/send", {
                service_id: "service_rv6anec",
                template_id: "template_nssm1mr",
                user_id: "QHVwy3-idEAd_3sVk",
                template_params: templateParams
            });

            console.log(`Email sent successfully to ${user.email}`);
        }

        alert("Notification process completed.");
    } catch (error) {
        console.error("Failed to send notification emails:", error);
    }
}

        
    </script>
</body>
</html>
