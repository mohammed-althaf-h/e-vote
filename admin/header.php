<?php
// Check if a session is already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include '../includes/db.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['registerno'])) {
    header("Location: ../auth/login.php");
    exit();
}

$registerno = $_SESSION['registerno'];
$is_admin = false;

$admin_check_sql = "SELECT isadmin FROM users WHERE registerno = ?";
$admin_check_stmt = $conn->prepare($admin_check_sql);
$admin_check_stmt->bind_param("s", $registerno);
$admin_check_stmt->execute();
$admin_check_result = $admin_check_stmt->get_result();
$admin_row = $admin_check_result->fetch_assoc();

if ($admin_row && $admin_row['isadmin'] == 1) {
    $is_admin = true;
}
$admin_check_stmt->close();

if (!$is_admin) {
    header("Location: ./index.php");
    exit();
}
?>
