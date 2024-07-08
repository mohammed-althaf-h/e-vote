<?php
include '../includes/db.php';

// Retrieve candidates from the database
$sql = "SELECT c.id, c.name, c.image_url, c.registerno, p.name AS position_name, p.id AS position_id 
        FROM candidates c 
        INNER JOIN positions p ON c.position_id = p.id";
$result = $conn->query($sql);

$candidates = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }
}

// Close connection
$conn->close();

echo json_encode($candidates);
?>
