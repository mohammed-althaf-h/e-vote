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

// Check if the user is eligible to vote
if (!$user['eligible']) {
    header("Location: voting.php");
    exit();
}

// Get position ID from query string
$position_id = isset($_GET['position_id']) ? intval($_GET['position_id']) : 0;

// Fetch position details
$sql = "SELECT * FROM positions WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $position_id);
$stmt->execute();
$position_result = $stmt->get_result();
$position = $position_result->fetch_assoc();

// Fetch candidates for the selected position
$sql = "SELECT id, name, image_url FROM candidates WHERE position_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $position_id);
$stmt->execute();
$candidates_result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote for <?php echo htmlspecialchars($position['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            margin-bottom: 20px;
            background-color: #000000;
        }
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: bold;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
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
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            border-radius: 10px;
        }
    </style>
    <script>
        function validateForm() {
            const radios = document.querySelectorAll('input[type="radio"]');
            let selected = false;
            for (const radio of radios) {
                if (radio.checked) {
                    selected = true;
                    break;
                }
            }
            if (!selected) {
                alert("Please select a candidate before submitting.");
                return
                false;
            }
            return true;
        }
    </script>
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
                        <a class="nav-link" href="voting.php">Back to Voting</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <h2 class="my-4 text-center"><b>Vote for <?php echo htmlspecialchars($position['name']); ?></h2></b>
        <?php if ($candidates_result->num_rows > 0): ?>
            <form action="submit_vote.php" method="post" onsubmit="return validateForm();">
                <input type="hidden" name="position_id" value="<?php echo $position_id; ?>">
                <div class="list-group">
                    <?php while ($candidate = $candidates_result->fetch_assoc()): ?>
                        <label class="list-group-item d-flex justify-content-between align-items-center">
                        <?php if ($candidate['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($candidate['image_url']); ?>" alt="<?php echo htmlspecialchars($candidate['name']); ?>" class="img-thumbnail" style="width: 50px; height: 50px;">
                            <?php endif; ?>
                            <span class="ms-2"><?php echo htmlspecialchars($candidate['name']); ?></span>
                            <input type="radio" name="candidate_id" value="<?php echo $candidate['id']; ?>" id="candidate_<?php echo $candidate['id']; ?>">
                        </label>
                    <?php endwhile; ?>
                </div>
                <div class="form-group mt-4 text-center">
                    <input type="submit" class="btn btn-primary" value="Submit Vote">
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-warning text-center" role="alert">
                No candidates available for this position.
            </div>
        <?php endif; ?>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/js/bootstrap.min.js"></script>
</body>
</html>
