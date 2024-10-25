<?php
session_start();

$requestUri = $_SERVER['REQUEST_URI'];

switch ($requestUri) {
    case '/':
    case '/index.php':
        include 'index.php';
        break;
    case '/login.php':
        include 'login.php';
        break;
    case '/register.php':
        include 'register.php';
        break;
    case '/dashboard.php':
        include 'dashboard.php';
        break;
    case '/add_case.php':
        include 'add_case.php';
        break;
    case '/process_case.php': // Ensure this route is included
        include 'process_case.php';
        break;
    case '/completed_cases.php':
        include 'completed_cases.php';
        break;
    case '/mark_done.php':
        include 'mark_done.php';
        break;
    case '/delete_case.php':
        include 'delete_case.php';
        break;
    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
?>
