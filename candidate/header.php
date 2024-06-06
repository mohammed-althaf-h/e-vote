<?php
session_start();
include '../includes/db.php';
include '../includes/csrf.php'; // Include the CSRF functions

// Check if the user is logged in and is a candidate
if (!isset($_SESSION['registerno']) || $_SESSION['iscand'] != 1) {
    header("Location: ../index.php");
    exit();
}

// Fetch candidate details
$registerno = $_SESSION['registerno'];
$sql = "SELECT * FROM candidates WHERE registerno = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $registerno);
$stmt->execute();
$result = $stmt->get_result();
$candidate = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #0f172a;
            color: #ffffff;
        }
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #1e293b;
            border-radius: 0.375rem;
        }
        .container h1 {
            text-align: center;
            margin-bottom: 1rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 0.375rem;
        }
        .form-group input[type="file"] {
            padding: 0;
        }
        .btn {
            display: inline-block;
            background-color: #3b82f6;
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.375rem;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
    <nav class="nav">
        <div>
            <a href="../index.php" class="nav_logo">
                <i class='bx bx-layer nav_logo-icon'></i>
                <span class="nav_logo-name">Candidate Portal</span>
            </a>
            <div class="nav_list">
                <a href="dashboard.php" class="nav_link active">
                    <i class='bx bx-grid-alt nav_icon'></i>
                    <span class="nav_name">Dashboard</span>
                </a>
                <a href="upload_manifesto.php" class="nav_link">
                    <i class='bx bx-upload nav_icon'></i>
                    <span class="nav_name">Upload Manifesto</span>
                </a>
                <a href="campaign_details.php" class="nav_link">
                    <i class='bx bx-campaign nav_icon'></i>
                    <span class="nav_name">Campaign Details</span>
                </a>
                <a href="../logout.php" class="nav_link">
                    <i class="bx bx-log-out nav_icon"></i>
                    <span class="nav_name">Sign Out</span>
                </a>
            </div>
        </div>
    </nav>
