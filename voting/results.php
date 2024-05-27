<?php
session_start();

// Include database connection
include '../includes/db.php';

// Fetch voting results
$sql = "SELECT positions.name AS position_name, candidates.name AS candidate_name, COUNT(votes.id) AS vote_count
        FROM votes
        INNER JOIN candidates ON votes.candidate_id = candidates.id
        INNER JOIN positions ON candidates.position_id = positions.id
        GROUP BY candidates.id
        ORDER BY positions.id, vote_count DESC";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <h2>Election Results</h2>
        <table>
            <thead>
                <tr>
                    <th>Position</th>
                    <th>Candidate</th>
                    <th>Votes</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['position_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                    <td><?php echo $row['vote_count']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
