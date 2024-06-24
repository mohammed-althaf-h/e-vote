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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $position_name = $_POST['position_name'];

    try {
        // Insert the new position into the positions table
        $sql = "INSERT INTO positions (name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $position_name);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => true, 'message' => 'Position added successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Position</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Add Position</h2>
    <div id="notification" class="alert alert-success" style="display: none;"></div>
    <form id="addPositionForm">
        <div class="form-group">
            <label for="position_name">Position Name:</label>
            <input type="text" id="position_name" name="position_name" class="form-control" required>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Add Position">
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
function showNotification(message, type = 'success') {
    var notification = $('#notification');
    notification.removeClass('alert-success alert-danger').addClass('alert-' + type);
    notification.text(message).show();
    setTimeout(function() {
        notification.fadeOut();
    }, 3000);
}

$(document).ready(function() {
    $('#addPositionForm').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: 'add_position.php',
            data: formData,
            success: function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    showNotification(res.message);
                    $('#addPositionForm')[0].reset();
                } else {
                    showNotification('Error adding position: ' + res.error, 'danger');
                }
            },
            error: function() {
                showNotification('Error adding position', 'danger');
            }
        });
    });
});
</script>
</body>
</html>
