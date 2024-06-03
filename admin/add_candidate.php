<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $position_id = $_POST['position_id'];
    $image_url = $_POST['image_url'];

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert the new candidate into the candidates table
        $sql = "INSERT INTO candidates (name, image_url, position_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $image_url, $position_id);
        $stmt->execute();
        $candidate_id = $stmt->insert_id; // Get the inserted candidate ID
        $stmt->close();

        // Generate a unique registration number and password
        $registerno = 'C' . str_pad($candidate_id, 8, '0', STR_PAD_LEFT); // Example format: C00000001
        $password = bin2hex(random_bytes(4)); // Generate a random 8-character password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Update the candidates table with the generated registerno
        $sql = "UPDATE candidates SET registerno = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $registerno, $candidate_id);
        $stmt->execute();
        $stmt->close();

        // Insert the new user into the users table
        $sql = "INSERT INTO users (name, registerno, password, email, isadmin, eligible, verified) VALUES (?, ?, ?, '', 0, 1, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $registerno, $hashed_password);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        echo json_encode(['success' => true, 'registerno' => $registerno, 'password' => $password]);
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

    $conn->close();
    exit();
}

// Fetch positions from the database
$sql = "SELECT * FROM positions";
$positions_result = $conn->query($sql);

$positions = array();
if ($positions_result->num_rows > 0) {
    while ($row = $positions_result->fetch_assoc()) {
        $positions[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Candidate</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Add Candidate</h2>
    <form id="addCandidateForm">
        <div class="form-group">
            <label for="name">Candidate Name:</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="position_id">Position:</label>
            <select id="position_id" name="position_id" class="form-control" required>
                <?php foreach ($positions as $position): ?>
                    <option value="<?php echo $position['id']; ?>"><?php echo htmlspecialchars($position['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="image_url">Image URL:</label>
            <input type="text" id="image_url" name="image_url" class="form-control">
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Add Candidate">
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    $('#addCandidateForm').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: 'add_candidate.php',
            data: formData,
            success: function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    alert('Candidate added successfully. Registration Number: ' + res.registerno + ' Password: ' + res.password);
                    $('#addCandidateForm')[0].reset();
                } else {
                    alert('Error adding candidate: ' + res.error);
                }
            },
            error: function() {
                alert('Error adding candidate');
            }
        });
    });
});
</script>
</body>
</html>
