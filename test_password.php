<?php
$password = 'your_password'; // Replace with the password you want to test
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
echo "Hashed Password: " . $hashedPassword . "\n";

// Simulate login
$inputPassword = 'your_password'; // Replace with the password you are trying to log in with
if (password_verify($inputPassword, $hashedPassword)) {
    echo "Password matches.";
} else {
    echo "Invalid password.";
}
?>
