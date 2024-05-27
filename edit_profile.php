<?php
session_start();
include 'includes/db.php';
include 'includes/csrf.php';

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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }

    $name = trim($_POST['name']);
    $password = trim($_POST['password']);
    $profile_photo_path = $user['profile_photo']; // Use existing photo if not updated

    // Handle profile photo upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $target_dir = "uploads/profile_photos/";
        
        // Check if the directory exists, if not create it
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
            $profile_photo_path = $target_file;
        } else {
            echo "Error uploading profile photo.";
        }
    }

    // Prepare update query
    $sql = "UPDATE users SET name = ?, profile_photo = ? WHERE registerno = ?";
    $params = [$name, $profile_photo_path, $registerno];
    $types = "sss";

    if (!empty($password)) {
        $hashedpass = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET name = ?, password = ?, profile_photo = ? WHERE registerno = ?";
        $params = [$name, $hashedpass, $profile_photo_path, $registerno];
        $types = "ssss";
    }

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo "Profile updated successfully.";
    } else {
        echo "Error updating profile: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="icon" type="image/png" href="assets/images/icons/favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendor/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="assets/vendor/css-hamburgers/hamburgers.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendor/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/util.css">
    <link rel="stylesheet" type="text/css" href="assets/css/main.css">
    <style>
        .edit-profile-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .profile-photo {
            margin-left: 30px;
        }
        .profile-photo img {
            max-width: 150px;
            height: auto;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100 edit-profile-container">
                <form class="login100-form validate-form" action="edit_profile.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    <span class="login100-form-title">
                        Edit Profile
                    </span>

                    <div class="wrap-input100 validate-input" data-validate="Valid name is required">
                        <input class="input100" type="text" name="name" placeholder="Name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-user" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="wrap-input100">
                        <input class="input100" type="password" name="password" placeholder="New Password (leave blank if not changing)">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="wrap-input100">
                        <input class="input100" type="file" name="profile_photo" accept="image/*">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-camera" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="container-login100-form-btn">
                        <button class="login100-form-btn" type="submit" name="update">
                            Update Profile
                        </button>
                    </div>
                </form>

                <div class="profile-photo">
                    <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile Photo">
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