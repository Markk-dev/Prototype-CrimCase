<?php
session_start();
include 'db.php';

function register($lname, $fname, $email, $hashedPassword) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO users (lname, fname, email, password) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$lname, $fname, $email, $hashedPassword]);
}

function login($email, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: dashboard.php');
            exit();
        } else {
            return "Invalid password.";
        }
    } else {
        return "Invalid email.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        // Check required fields
        if (empty($_POST['lname']) || empty($_POST['fname']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirm'])) {
            header('Location: register.php?error=' . urlencode("Error: All fields are required."));
            exit();
        }

        $email = $_POST['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_ends_with($email, '@gmail.com')) {
            header('Location: register.php?error=' . urlencode("Error: Please enter a valid Gmail address."));
            exit();
        }

        $lname = $_POST['lname'];
        $fname = $_POST['fname'];
        if (!preg_match('/^[a-zA-Z\s]+$/', $lname) || !preg_match('/^[a-zA-Z\s]+$/', $fname)) {
            header('Location: register.php?error=' . urlencode("Error: Names must only contain letters and spaces."));
            exit();
        }

        $password = $_POST['password'];
        if ($password !== $_POST['confirm']) {
            header('Location: register.php?error=' . urlencode("Error: Passwords do not match."));
            exit();
        }

        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[\W_]/', $password)) {
            header('Location: register.php?error=' . urlencode("Error: Password must be at least 8 characters long, contain at least one uppercase letter, and one special character."));
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $checkUserSql = "SELECT * FROM users WHERE email = ?";
        $stmt = $pdo->prepare($checkUserSql);
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            header('Location: register.php?error=' . urlencode("Error: A user with this email already exists."));
            exit();
        }

        if (register($lname, $fname, $email, $hashedPassword)) {
            header('Location: login.php');
            exit();
        } else {
            header('Location: register.php?error=' . urlencode("Error: Unable to register user. Please try again."));
            exit();
        }
    } elseif (isset($_POST['login'])) {
        if (empty($_POST['email']) || empty($_POST['password'])) {
            header('Location: login.php?error=' . urlencode("Error: All fields are required."));
            exit();
        }

        $email = $_POST['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: login.php?error=' . urlencode("Error: Please enter a valid email address."));
            exit();
        }

        $loginError = login($email, $_POST['password']);
        if ($loginError) {
            header('Location: login.php?error=' . urlencode($loginError));
            exit();
        }
    }
}
?>
