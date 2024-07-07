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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #0f172a;
            color: #ffffff;
        }

        .navbar {
            background-color: #010103;
        }

        .navbar-nav .nav-link {
            color: #ffffff;
        }

        .navbar-nav .nav-link:hover {
            text-decoration: underline;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
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
            background-size: cover;
        }

        @media (max-width: 768px) {
            .hero {
                background-size: cover;
                padding: 1rem;
                height: 60vh;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
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

        .profile {
            position: relative;
            display: inline-block;
        }

        .profile img {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
        }

        .profile .profile-menu {
            display: none;
            position: absolute;
            right: 0;
            background-color: #ffffff;
            color: #000000;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 0.375rem;
        }

        .profile .profile-menu a {
            color: #000000;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .profile .profile-menu a:hover {
            background-color: #ddd;
        }

        .profile:hover .profile-menu {
            display: block;
        }
    </style>
</head>
<body>
    <h1>LOL</h1>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="index.php">E-Voting System</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <?php if (isset($_SESSION['registerno'])): ?>
                    <li class="nav-item"><a class="nav-link" href="./voting/voting.php">Vote</a></li>
                    <?php
                    // Include database connection
                    include 'includes/db.php';

                    // Check if user is admin
                    $registerno = $_SESSION['registerno'];
                    $is_admin = false;

                    // Check if the user is an admin
                    $admin_check_sql = "SELECT isadmin, profile_photo FROM users WHERE registerno = ?";
                    $admin_check_stmt = $conn->prepare($admin_check_sql);
                    $admin_check_stmt->bind_param("s", $registerno);
                    $admin_check_stmt->execute();
                    $admin_check_result = $admin_check_stmt->get_result();
                    $admin_row = $admin_check_result->fetch_assoc();

                    if ($admin_row && $admin_row['isadmin'] == 1) {
                        $is_admin = true;
                    }
                    $profile_photo = $admin_row['profile_photo'];
                    $admin_check_stmt->close();
                    ?>

                    <?php if ($is_admin): ?>
                        <!-- Navigation items for admins -->
                        <li class="nav-item"><a class="nav-link" href="./admin/">Dashboard</a></li>
                    <?php endif; ?>
                    <li class="nav-item dropdown profile">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Profile Photo">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right profile-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="view_profile.php">My Profile</a>
                            <?php if ($is_admin): ?>
                                <a class="dropdown-item" href="./admin/">Admin Panel</a>
                            <?php endif; ?>
                            <a class="dropdown-item" href="logout.php">Logout</a>
                        </div>
                    </li>
                <?php else: ?>
                    <!-- Navigation items for non-logged-in users -->
                    <li class="nav-item"><a class="nav-link" href="./auth/login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="./auth/register.php">Register</a></li>
                <?php endif; ?> 
                <!-- Common navigation item -->
                <li class="nav-item"><a class="nav-link" href="./voting/results.php">Results</a></li>
            </ul>
        </div>
    </nav>

    <section class="hero">
        <h1>Welcome to the E-Voting System</h1>
        <p>Your platform for a transparent and secure voting experience.</p>
        <div>
            <?php if(isset($_SESSION['registerno'])): ?>
                <a href="voting/voting.php" class="btn btn-primary">Go to Voting Page</a>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            <?php else: ?>
                <a href="auth/login.php" class="btn btn-primary">Login</a>
                <a href="auth/register.php" class="btn btn-secondary">Register</a>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> E-Voting System. All rights reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
