<?php
// Check if a session is already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include '../includes/db.php';

// Check if the user is logged in and is an cand
if (!isset($_SESSION['registerno'])) {
    header("Location: ../auth/login.php");
    exit();
}

$registerno = $_SESSION['registerno'];
$is_cand = false;

$cand_check_sql = "SELECT iscand FROM users WHERE registerno = ?";
$cand_check_stmt = $conn->prepare($cand_check_sql);
$cand_check_stmt->bind_param("s", $registerno);
$cand_check_stmt->execute();
$cand_check_result = $cand_check_stmt->get_result();
$cand_row = $cand_check_result->fetch_assoc();

if ($cand_row && $cand_row['iscand'] == 1) {
    $is_cand = true;
}
$cand_check_stmt->close();

if (!$is_cand) {
    header("Location: ./index.php");
    exit();
}
?>
