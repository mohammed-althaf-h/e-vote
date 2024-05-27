<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $position_id = $_POST['position_id'];
    $image_url = $_POST['image_url'];

    $sql = "INSERT INTO candidates (name, image_url, position_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $image_url, $position_id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true]);
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
                    alert('Candidate added successfully');
                    $('#addCandidateForm')[0].reset();
                } else {
                    alert('Error adding candidate');
                }
            },
            error: function() {
                alert('Error adding candidate');
            }
        });
    });
});
</script>
