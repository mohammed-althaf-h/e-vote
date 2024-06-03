<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['registerno'])) {
    header("Location: login.php");
    exit();
}

$registerno = $_SESSION['registerno'];

// Fetch user data
$sql = "SELECT * FROM users WHERE registerno = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $registerno);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <link rel="icon" type="image/png" href="assets/images/icons/favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendor/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="assets/vendor/css-hamburgers/hamburgers.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendor/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/util.css">
    <link rel="stylesheet" type="text/css" href="assets/css/main.css">
    <style>
        .view-profile-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .profile-photo img {
            max-width: 150px;
            height: auto;
            border-radius: 50%;
            margin-bottom: 20px;
        }
        .profile-details {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100 view-profile-container">
                <span class="login100-form-title">
                    Profile
                </span>

                <div class="profile-photo">
                    <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile Photo">
                </div>

                <div class="profile-details">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Register Number:</strong> <?php echo htmlspecialchars($user['registerno']); ?></p>
                </div>

                <div class="container-login100-form-btn">
                    <a class="login100-form-btn" href="edit_profile.php">
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="assets/vendor/bootstrap/js/popper.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/vendor/select2/select2.min.js"></script>
    <script src="assets/vendor/tilt/tilt.jquery.min.js"></script>
    <script>
        $('.js-tilt').tilt({
            scale: 1.1
        })
    </script>
    <script src="assets/js/main.js"></script>
</body>
</html>
