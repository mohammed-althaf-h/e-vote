<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Voting System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="./assets/images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="./assets/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="./assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="./assets/vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="./assets/vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="./assets/vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="./assets/css/util.css">
	<link rel="stylesheet" type="text/css" href="/assets/css/main.css">
<!--===============================================================================================-->
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <?php if (isset($_SESSION['registerno'])): ?>
                    <li><a href="/../voting/voting.php">Vote</a></li>

                    <?php
                    // Include database connection
                    include 'db.php';

                    // Check if user is admin
                    $registerno = $_SESSION['registerno'];
                    $is_admin = false;

                    // Check if the user is an admin
                    $admin_check_sql = "SELECT isadmin FROM users WHERE registerno = ?";
                    $admin_check_stmt = $conn->prepare($admin_check_sql);
                    $admin_check_stmt->bind_param("s", $registerno);
                    $admin_check_stmt->execute();
                    $admin_check_result = $admin_check_stmt->get_result();
                    $admin_row = $admin_check_result->fetch_assoc();

                    if ($admin_row && $admin_row['isadmin'] === 1) {
                        $is_admin = true;
                    }
                    $admin_check_stmt->close();
                    ?>

                    <?php if ($is_admin = true): ?>
                        <!-- Navigation items for admins -->
                        <li><a href="./admin/dashboard.php">Dashboard</a></li>
                        <li><a href=".  /logout.php">Logout</a></li>
                    <?php else: ?>
                        <!-- Navigation items for regular users -->
                        <li><a href="../logout.php">Logout</a></li>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- Navigation items for non-logged-in users -->
                    <li><a href="./auth/login.php">Login</a></li>
                    <li><a href="./auth/register.php">Register</a></li>
                <?php endif; ?>
                
                <!-- Common navigation item -->
                <li><a href="../voting/results.php">Results</a></li>
            </ul>
        </nav>
    </header>
</body>
</html>
