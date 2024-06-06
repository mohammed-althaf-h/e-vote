<?php
session_start();
include 'includes/db.php';
include 'includes/csrf.php'; // Include the CSRF functions

// Check if the user is logged in and is a candidate
if (!isset($_SESSION['registerno']) || $_SESSION['iscand'] != 1) {
    header("Location: ../index.php");
    exit();
}

// Process form submission for manifesto upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_manifesto'])) {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }

    // Handle file upload
    if ($_FILES['manifesto_file']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../uploads/manifestos/";
        $target_file = $target_dir . basename($_FILES["manifesto_file"]["name"]);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check file type
        if ($file_type != "pdf") {
            echo "Only PDF files are allowed.";
        } else {
            if (move_uploaded_file($_FILES["manifesto_file"]["tmp_name"], $target_file)) {
                // Save the manifesto file path to the database
                $sql = "UPDATE candidates SET manifesto = ? WHERE registerno = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $target_file, $_SESSION['registerno']);
                $stmt->execute();
                $stmt->close();
                echo "The file " . htmlspecialchars(basename($_FILES["manifesto_file"]["name"])) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
}

// Process form submission for campaign details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_campaign'])) {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }

    $campaign_details = trim($_POST['campaign_details']);
    $registerno = $_SESSION['registerno'];

    // Save campaign details to the database
    $sql = "UPDATE candidates SET campaign_details = ? WHERE registerno = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $campaign_details, $registerno);
    $stmt->execute();
    $stmt->close();
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
                <a href="#" class="nav_link active" onclick="showTab('tab-dashboard')">
                    <i class='bx bx-grid-alt nav_icon'></i>
                    <span class="nav_name">Dashboard</span>
                </a>
                <a href="#" class="nav_link" onclick="showTab('tab-upload')">
                    <i class='bx bx-upload nav_icon'></i>
                    <span class="nav_name">Upload Manifesto</span>
                </a>
                <a href="#" class="nav_link" onclick="showTab('tab-campaign')">
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
    
    <div class="container" id="tab-dashboard">
        <h1>Dashboard</h1>
        <p>Welcome to your dashboard. Use the navigation links to manage your manifesto and campaign details.</p>
    </div>

    <div class="container" id="tab-upload" style="display: none;">
        <h1>Upload Manifesto</h1>
        <form action="candidate.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            <div class="form-group">
                <label for="manifesto_file">Upload Manifesto (PDF only):</label>
                <input type="file" name="manifesto_file" id="manifesto_file" accept=".pdf" required>
            </div>
            <div class="form-group">
                <button class="btn" type="submit" name="upload_manifesto">Upload Manifesto</button>
            </div>
        </form>
    </div>

    <div class="container" id="tab-campaign" style="display: none;">
        <h1>Campaign Details</h1>
        <form action="candidate.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            <div class="form-group">
                <label for="campaign_details">Campaign Details:</label>
                <textarea name="campaign_details" id="campaign_details" rows="5" required><?php echo htmlspecialchars($candidate['campaign_details']); ?></textarea>
            </div>
            <div class="form-group">
                <button class="btn" type="submit" name="submit_campaign">Submit Campaign Details</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
    function showTab(tabId) {
        $('.container').hide();
        $('#' + tabId).show();
        $('.nav_link').removeClass('active');
        $('.nav_link[onclick="showTab(\'' + tabId + '\')"]').addClass('active');
    }
    </script>
</body>
</html>
