<?php
session_start();

// Include database connection
include '../includes/db.php';

// Check if the user is logged in
if (!isset($_SESSION['registerno'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch user details to check eligibility
$registerno = $_SESSION['registerno'];
$sql = "SELECT eligible FROM users WHERE registerno = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $registerno);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch current voting status
$sql = "SELECT voting_enabled FROM settings WHERE id = 1";
$result = $conn->query($sql);
$voting_enabled = false;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $voting_enabled = $row['voting_enabled'];
}

// Fetch positions from the database
$sql = "SELECT * FROM positions";
$positions_result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        .container {
            max-width: 900px;
            margin: auto;
            background-color: #0d1117;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .card {
            border: none;
            border-radius: 10px;
            background-color: #21262d;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }
        .card h5 {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .alert {
            border-radius: 10px;
        }
        .footer {
            text-align: center;
            padding: 1rem 0;
            background-color: #0d1117;
            color: #ffffff;
            margin-top: auto;
        }
        @media (max-width: 767px) {
            .navbar-brand {
                font-size: 1.5rem;
            }
            .container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">Voting System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <h2 class="my-4 text-center"><b>Voting Page</b></h2>
        <?php if ($voting_enabled): ?>
            <?php if ($user['eligible']): ?>
                <div class="row">
                    <?php while ($position = $positions_result->fetch_assoc()): ?>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    <h5 class="card-title"><?php echo htmlspecialchars($position['name']); ?></h5>
                                    <a href="vote.php?position_id=<?php echo $position['id']; ?>" class="btn btn-primary">Vote <i class="fas fa-vote-yea"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center" role="alert">
                    You are not eligible to vote. Please contact the administrator for more information.
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info text-center" role="alert">
                Voting is not currently enabled. Please check back later.
            </div>
        <?php endif; ?>
    </div>
    <div class="footer">
        <p>&copy; 2024 E-Voting System</p>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/js/bootstrap.min.js"></script>
</body>
</html>
