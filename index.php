<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Voting System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #0f172a;
            color: #ffffff;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background-color: #010103;
        }

        .navbar a {
            color: #ffffff;
            text-decoration: none;
            padding: 0.5rem 1rem;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .navbar .logo {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .navbar ul {
            list-style-type: none;
            display: flex;
            gap: 1rem;
            padding: 0;
            margin: 0;
        }

        .hero {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 2rem;
            height: 80vh;
            background: linear-gradient(145deg, rgba(15, 23, 42, 1) 0%, rgba(30, 41, 59, 1) 100%);
            background-image: url('https://wallpapers.com/images/featured/dark-5u7v1sbwoi6hdzsb.jpg');
            background-position: center;
            background-size: 100% 100%;
        }

        @media (max-width: 768px) {
            .hero {
                background-size: auto;
            }
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .hero .btn {
            background-color: #3b82f6;
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.375rem;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .hero .btn:hover {
            background-color: #2563eb;
        }

        .hero .btn + .btn {
            margin-left: 1rem;
        }

        .footer {
            text-align: center;
            padding: 1rem 0;
            background-color: #1e293b;
        }

        .footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">E-Voting System</div>
        <div>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <?php if (isset($_SESSION['registerno'])): ?>
                    <li><a href="./voting/voting.php">Vote</a></li>
                    <?php
                    // Include database connection
                    include 'includes/db.php';

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

                    if ($admin_row && $admin_row['isadmin'] == 1) {
                        $is_admin = true;
                    }
                    $admin_check_stmt->close();
                    ?>

                    <?php if ($is_admin): ?>
                        <!-- Navigation items for admins -->
                        <li><a href="./admin/">Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <!-- Navigation items for non-logged-in users -->
                    <li><a href="./auth/login.php">Login</a></li>
                    <li><a href="./auth/register.php">Register</a></li>
                <?php endif; ?> 
                <!-- Common navigation item -->
                <li><a href="./voting/results.php">Results</a></li>
            </ul>
        </div>
    </nav>

    <section class="hero">
        <h1>Welcome to the E-Voting System</h1>
        <p>Your platform for a transparent and secure voting experience.</p>
        <div>
            <?php if(isset($_SESSION['registerno'])): ?>
                <a href="voting/voting.php" class="btn">Go to Voting Page</a>
                <a href="logout.php" class="btn">Logout</a>
            <?php else: ?>
                <a href="auth/login.php" class="btn">Login</a>
                <a href="auth/register.php" class="btn">Register</a>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>
