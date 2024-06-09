<?php
// Check if a session is already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../includes/db.php'; // Include your database connection
include '../includes/csrf.php'; // Include your CSRF functions

// Fetch candidate details
$registerno = $_SESSION['registerno'];
$sql = "SELECT campaign_details FROM candidates WHERE registerno = ?";
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
    <title>Campaign Details</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        <form id="campaign_form" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            <div class="form-group">
                <label for="campaign_details">Campaign Details:</label>
                <textarea class="form-control" name="campaign_details" id="campaign_details" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit" name="submit_campaign">Submit Campaign Details</button>
            </div>
        </form>
        <div id="message"></div>
        <h2>Current Campaign Details</h2>
        <div id="current_campaign_details">
            <?php echo htmlspecialchars($candidate['campaign_details'] ?? 'No campaign details available.'); ?>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('#campaign_form').on('submit', function(event) {
            event.preventDefault();
            
            var formData = $(this).serialize();
            
            $.ajax({
                url: 'ajax_campaign_details.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#message').html(response);
                    $('#current_campaign_details').text($('#campaign_details').val());
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#message').html('<p>Error: ' + textStatus + '</p>');
                }
            });
        });
    });
    </script>
</body>
</html>
