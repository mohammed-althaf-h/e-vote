<?php
include 'header.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['registerno']) || $_SESSION['isadmin'] != 1) {
    header("Location: ../auth/login.php");
    exit();
}

// Include database connection
include '../includes/db.php';

// Handle AJAX requests for eligibility change
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['change_eligibility'])) {
        $registerno = $_POST['registerno'];
        $new_eligibility = $_POST['eligible'] ? 0 : 1;
        $sql = "UPDATE users SET eligible = ? WHERE registerno = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $new_eligibility, $registerno);
        $stmt->execute();
        $stmt->close();
        
        echo json_encode(['success' => true, 'new_eligibility' => $new_eligibility]);
        exit();
    }
    
    // Handle AJAX requests for verification change
    if (isset($_POST['change_verification'])) {
        $registerno = $_POST['registerno'];
        $new_verification = $_POST['verified'] ? 0 : 1;
        $sql = "UPDATE users SET verified = ? WHERE registerno = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $new_verification, $registerno);
        $stmt->execute();
        $stmt->close();
        
        echo json_encode(['success' => true, 'new_verification' => $new_verification]);
        exit();
    }
}

// Retrieve users from the database
$sql = "SELECT registerno, email, verified, eligible, iscand FROM users WHERE iscand != 1";
$result = $conn->query($sql);

$users = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>View Users</h2>
        <div id="notification" class="alert alert-success" style="display: none;"></div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Register Number</th>
                        <th>Email</th>
                        <th>Verified</th>
                        <th>Eligible</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr id="user-<?php echo htmlspecialchars($user['registerno']); ?>">
                        <td><?php echo htmlspecialchars($user['registerno']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td class="user-verified"><?php echo $user['verified'] ? 'Yes' : 'No'; ?></td>
                        <td class="user-eligible"><?php echo $user['eligible'] ? 'Yes' : 'No'; ?></td>
                        <td>
                            <form class="userForm" data-registerno="<?php echo htmlspecialchars($user['registerno']); ?>">
                                <input type="hidden" name="verified" value="<?php echo $user['verified']; ?>">
                                <input type="hidden" name="eligible" value="<?php echo $user['eligible']; ?>">
                                <button type="button" class="btn btn-secondary btn-sm change-verification">
                                    <?php echo $user['verified'] ? 'Unverified' : 'Set Verified'; ?>
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm change-eligibility">
                                    <?php echo $user['eligible'] ? 'Set Not Eligible' : 'Set Eligible'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        // Handle verification change
        $('.change-verification').click(function() {
            var form = $(this).closest('form');
            var registerno = form.data('registerno');
            var verified = form.find('input[name="verified"]').val();
            
            $.ajax({
                type: 'POST',
                url: 'view_users.php',
                data: {
                    registerno: registerno,
                    change_verification: true,
                    verified: verified
                },
                success: function(response) {
                    var res = JSON.parse(response);
                    if (res.success) {
                        var row = $('#user-' + registerno);
                        row.find('.user-verified').text(res.new_verification ? 'Yes' : 'No');
                        form.find('input[name="verified"]').val(res.new_verification);
                        form.find('.change-verification').text(res.new_verification ? 'Unverified' : 'Set Verified');
                        showNotification('Verification status changed successfully');
                    } else {
                        showNotification('Error changing verification status', 'danger');
                    }
                },
                error: function() {
                    showNotification('Error changing verification status', 'danger');
                }
            });
        });

        // Handle eligibility change
        $('.change-eligibility').click(function() {
            var form = $(this).closest('form');
            var registerno = form.data('registerno');
            var eligible = form.find('input[name="eligible"]').val();
            
            $.ajax({
                type: 'POST',
                url: 'view_users.php',
                data: {
                    registerno: registerno,
                    change_eligibility: true,
                    eligible: eligible
                },
                success: function(response) {
                    var res = JSON.parse(response);
                    if (res.success) {
                        var row = $('#user-' + registerno);
                        row.find('.user-eligible').text(res.new_eligibility ? 'Yes' : 'No');
                        form.find('input[name="eligible"]').val(res.new_eligibility);
                        form.find('.change-eligibility').text(res.new_eligibility ? 'Set Not Eligible' : 'Set Eligible');
                        showNotification('Eligibility status changed successfully');
                    } else {
                        showNotification('Error changing eligibility status', 'danger');
                    }
                },
                error: function() {
                    showNotification('Error changing eligibility status', 'danger');
                }
            });
        });

        function showNotification(message, type = 'success') {
            var notification = $('#notification');
            notification.removeClass('alert-success alert-danger').addClass('alert-' + type);
            notification.text(message).show();
            setTimeout(function() {
                notification.fadeOut();
            }, 3000);
        }
    });
    </script>
</body>
</html>
