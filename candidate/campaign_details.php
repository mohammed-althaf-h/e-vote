<?php
include 'header.php';
include '../includes/csrf.php';

// Fetch candidate details
$registerno = $_SESSION['registerno'];
$sql = "SELECT campaign_details FROM candidates WHERE registerno = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $registerno);
$stmt->execute();
$result = $stmt->get_result();
$candidate = $result->fetch_assoc();
$stmt->close();

// Process form submission for campaign details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_campaign'])) {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }

    $campaign_details = trim($_POST['campaign_details']);

    // Save campaign details to the database
    $sql = "UPDATE candidates SET campaign_details = ? WHERE registerno = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $campaign_details, $registerno);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign Details</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        #tab-campaign {
            margin-top: 50px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #343a40;
        }
        .form-group label {
            color: #495057;
            font-weight: bold;
        }
        .btn {
            background-color: #007bff;
            color: #ffffff;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container" id="tab-campaign">
        <h1>Campaign Details</h1>
        <form action="campaign_details.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            <div class="form-group">
                <label for="campaign_details">Campaign Details:</label>
                <textarea class="form-control" name="campaign_details" id="campaign_details" rows="5" required><?php echo htmlspecialchars($candidate['campaign_details'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit" name="submit_campaign">Submit Campaign Details</button>
            </div>
        </form>
    </div>
</body>
</html>
