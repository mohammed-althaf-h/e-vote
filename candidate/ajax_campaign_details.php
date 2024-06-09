<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include '../includes/db.php'; // Include your database connection
include '../includes/csrf.php'; // Include your CSRF functions

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }

    $registerno = $_SESSION['registerno'];
    $campaign_details = trim($_POST['campaign_details']);

    // Save campaign details to the database
    $sql = "UPDATE candidates SET campaign_details = ? WHERE registerno = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Prepare statement failed: " . $conn->error;
        exit;
    }
    $stmt->bind_param("ss", $campaign_details, $registerno);
    if ($stmt->execute()) {
        echo "Campaign details have been updated.";
    } else {
        echo "Execute statement failed: " . $stmt->error;
    }
    $stmt->close();
}
?>
