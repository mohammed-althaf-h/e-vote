<?php
session_start();
include '../includes/db.php';
include '../includes/csrf.php'; // Include the CSRF functions

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }

    $registerno = trim($_POST['registerno']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    // Generate verification code
    $verificationCode = bin2hex(random_bytes(16)); // Generate a 32-character hexadecimal string

    // Hash the password
    $hashedpass = password_hash($password, PASSWORD_BCRYPT);

    // Prepare the SQL statement
    $sql = "INSERT INTO users (registerno, password, email, verification_code) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("ssss", $registerno, $hashedpass, $email, $verificationCode);

    // Execute query
    if ($stmt->execute()) {
        // Send verification email
        $to = $email;
        $subject = "Verify Your Email";
        $message = "Click the following link to verify your email: http://inilax.com/auth/verify.php?code=$verificationCode";
        $headers = "From: your@example.com" . "\r\n" .
                   "Reply-To: your@example.com" . "\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        if (mail($to, $subject, $message, $headers)) {
            echo "User registered successfully. Please verify your email.";
        } else {
            echo "Error sending verification email. Debug info: " . print_r(error_get_last(), true);
        }
    } else {
        echo "Error registering user: " . $stmt->error;
    }
    
    // Close statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="icon" type="image/png" href="../assets/images/icons/favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/vendor/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="../assets/vendor/css-hamburgers/hamburgers.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/vendor/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/util.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/main.css">
</head>
<body>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <div class="login100-pic js-tilt" data-tilt>
                    <img src="../assets/images/img-01.png" alt="IMG">
                </div>

                <form class="login100-form validate-form" action="register.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    <span class="login100-form-title">
                        User Registration
                    </span>

                    <div class="wrap-input100 validate-input" data-validate="Valid register number is required">
                        <input class="input100" type="text" name="registerno" placeholder="Register Number" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-user" aria-hidden="true"></i>
                        </span>
                    </div>
                    
                    <div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz">
                        <input class="input100" type="email" name="email" placeholder="Email" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="wrap-input100 validate-input" data-validate="Password is required">
                        <input class="input100" type="password" name="password" placeholder="Password" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                        </span>
                    </div>
                    
                    <div class="container-login100-form-btn">
                        <button class="login100-form-btn" type="submit" name="register">
                            Register
                        </button>
                    </div>

                    <div class="text-center p-t-136">
                        <a class="txt2" href="login.php">
                            Already have an account? Login
                            <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="../assets/vendor/bootstrap/js/popper.js"></script>
    <script src="../assets/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/vendor/select2/select2.min.js"></script>
    <script src="../assets/vendor/tilt/tilt.jquery.min.js"></script>
    <script>
        $('.js-tilt').tilt({
            scale: 1.1
        })
    </script>
    <script src="../assets/js/main.js"></script>
</body>
</html>
