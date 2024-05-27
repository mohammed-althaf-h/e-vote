<?php
session_start();

// Include database connection
include '../includes/db.php';

// Get the form data
$position_id = isset($_POST['position_id']) ? intval($_POST['position_id']) : 0;
$candidate_id = isset($_POST['candidate_id']) ? intval($_POST['candidate_id']) : 0;

// Get registerno from session
if (isset($_SESSION['registerno'])) {
    $registerno = $_SESSION['registerno'];

    // Check if a candidate is selected
    if ($candidate_id > 0) {
        // Check if the registerno exists in the users table
        $check_sql = "SELECT COUNT(*) as count FROM users WHERE registerno = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $registerno);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $row = $check_result->fetch_assoc();

        if ($row['count'] > 0) {
            // Ensure the user has not already voted for this position
            $vote_check_sql = "SELECT COUNT(*) as count FROM votes WHERE registerno = ? AND position_id = ?";
            $vote_check_stmt = $conn->prepare($vote_check_sql);
            $vote_check_stmt->bind_param("si", $registerno, $position_id);
            $vote_check_stmt->execute();
            $vote_check_result = $vote_check_stmt->get_result();
            $vote_row = $vote_check_result->fetch_assoc();

            if ($vote_row['count'] == 0) {
                // Insert the vote
                $sql_insert = "INSERT INTO votes (registerno, position_id, candidate_id) VALUES (?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("sii", $registerno, $position_id, $candidate_id);
                if ($stmt_insert->execute()) {
                    echo "Vote submitted successfully.";
                } else {
                    echo "Error submitting vote.";
                }
                $stmt_insert->close();
            } else {
                echo "You have already voted for this position.";
            }
        } else {
            echo "Invalid user.";
        }

        $check_stmt->close();
        $vote_check_stmt->close();
    } else {
        echo "Please select a candidate before submitting your vote.";
    }
} else {
    echo "Session not found. Please log in.";
}

$conn->close();
?>
