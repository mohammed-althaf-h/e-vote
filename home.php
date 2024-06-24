<?php
session_start();

if (!isset($_SESSION['registerno'])) {
    header('Location:auth/login.php');
    exit();
}

include 'includes/db.php';

$registerno = $_SESSION['registerno'];
$sql_user = "SELECT name, profile_photo FROM users WHERE registerno = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $registerno);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();
$stmt_user->close();

$sql_candidates = "SELECT id, name, campaign_details, manifesto FROM candidates";
$result_candidates = $conn->query($sql_candidates);

$is_admin = false;
$sql_admin = "SELECT isadmin FROM users WHERE registerno = ?";
$stmt_admin = $conn->prepare($sql_admin);
$stmt_admin->bind_param("s", $registerno);
$stmt_admin->execute();
$result_admin = $stmt_admin->get_result();
$admin_row = $result_admin->fetch_assoc();
if ($admin_row && $admin_row['isadmin'] == 1) {
    $is_admin = true;
}
$stmt_admin->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
               body {
            font-family: 'Roboto', sans-serif;
            background-color: #1e293b;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            margin-bottom: 20px;
            background-color: #0d1117;
        }
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: bold;
        }
        .wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .profile-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #ffffff;
        }
        
        .profile-menu {
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
        .profile-menu a {
            color: #000000;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .profile-menu a:hover {
            background-color: #ddd;
        }
        .profile:hover .profile-menu {
            display: block;
        }
        .container {
            max-width: 1200px;
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
            background-color: #2d3748;
            color: #ffffff;
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card h3, .card h5 {
            font-weight: 700;
        }
        .btn-primary {
            background-color: #1829bb;
            border: none;
        }
        .footer {
            text-align: center;
            padding: 1rem 0;
            background-color: #0d1117;
            color: #ffffff;
            margin-top: auto;
        }
        .nav-tabs .nav-link {
            background-color: #2d3748;
            color: #ffffff;
            border: none;
            border-radius: 0.375rem 0.375rem 0 0;
            margin-right: 2px;
        }
        .nav-tabs .nav-link.active {
            background-color: #1829bb;
            color: #c5cdd5  ;
        }
        @media (max-width: 767px) {
            .profile-menu {
                right: 0;
                left: auto;
            }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">E-Voting System</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item profile">
                    <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile Photo" class="profile-photo">
                    <div class="profile-menu">
                        <a href="view_profile.php">My Profile</a>
                        <?php if ($is_admin): ?>
                            <a href="./admin/">Admin Panel</a>
                        <?php endif; ?>
                        <a href="logout.php">Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Tabs Navigation -->
    <div class="container">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="candidates-tab" data-toggle="tab" href="#candidates" role="tab" aria-controls="candidates" aria-selected="false">Candidates</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="voting-tab" data-toggle="tab" href="#voting" role="tab" aria-controls="voting" aria-selected="false">Vote</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="results-tab" data-toggle="tab" href="#results" role="tab" aria-controls="results" aria-selected="false">Results</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="card">
                    <div class="card-body">
                        <h3>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h3>
                        <p>You can view the candidates and their manifestos and vote accordingly</p>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="candidates" role="tabpanel" aria-labelledby="candidates-tab">
                <div class="card">
                    <div class="card-body">
                        <h3>Candidates' Details</h3>
                        <div id="candidates-list">
                            <?php if ($result_candidates->num_rows > 0): ?>
                                <?php while ($candidate = $result_candidates->fetch_assoc()): ?>
                                    <div class="candidate-card">
                                        <h5><?php echo htmlspecialchars($candidate['name']); ?></h5>
                                        <p><?php echo htmlspecialchars($candidate['campaign_details']); ?></p>
                                        <?php if (!empty($candidate['manifesto'])): ?>
                                            <a href="<?php echo htmlspecialchars($candidate['manifesto']); ?>" class="btn btn-primary" target="_blank">View Manifesto</a>
                                        <?php else: ?>
                                            <p>No manifesto available.</p>
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p>No candidates found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="voting" role="tabpanel" aria-labelledby="voting-tab">
                <div class="card">
                    <div class="card-body">
                        <h3>Vote</h3>
                        <p>Cast your vote <a href="./voting/voting.php">here</a>.</p>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="results" role="tabpanel" aria-labelledby="results-tab">
                <div class="card">
                    <div class="card-body">
                        <h3>Voting Results</h3>
                        <p>View the current voting results <a href="./voting/results.php">here</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; 2024 E-Voting System</p>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function () {
    $('#myTab a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // Optional: Load content dynamically (e.g., via AJAX) for each tab if needed
});
</script>
</body>
</html>

<?php
$conn->close();
?>
