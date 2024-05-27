<?php
include '../includes/db.php';

// Handle delete candidate request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_candidate'])) {
    $candidate_id = $_POST['candidate_id'];
    $sql = "DELETE FROM candidates WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $candidate_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => true]);
    exit();
}

// Handle edit candidate request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_candidate'])) {
    $candidate_id = $_POST['candidate_id'];
    $name = $_POST['name'];
    $position_id = $_POST['position_id'];
    $image_url = $_POST['image_url'];

    $sql = "UPDATE candidates SET name = ?, position_id = ?, image_url = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisi", $name, $position_id, $image_url, $candidate_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => true]);
    exit();
}

// Retrieve candidates from the database
$sql = "SELECT c.id, c.name, c.image_url, p.name AS position_name, p.id AS position_id 
        FROM candidates c 
        INNER JOIN positions p ON c.position_id = p.id";
$result = $conn->query($sql);

$candidates = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }
}

// Retrieve positions from the database for the edit form
$sql = "SELECT id, name FROM positions";
$result = $conn->query($sql);

$positions = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $positions[] = $row;
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Candidates</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Manage Candidates</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Image URL</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($candidates as $candidate): ?>
            <tr id="candidate-<?php echo htmlspecialchars($candidate['id']); ?>">
                <td class="candidate-name"><?php echo htmlspecialchars($candidate['name']); ?></td>
                <td class="candidate-position"><?php echo htmlspecialchars($candidate['position_name']); ?></td>
                <td class="candidate-image-url"><?php echo htmlspecialchars($candidate['image_url']); ?></td>
                <td>
                    <button class="btn btn-danger delete-candidate" data-id="<?php echo htmlspecialchars($candidate['id']); ?>">Delete</button>
                    <button class="btn btn-primary" onclick="editCandidate('<?php echo htmlspecialchars($candidate['id']); ?>', '<?php echo htmlspecialchars(addslashes($candidate['name'])); ?>', '<?php echo htmlspecialchars($candidate['position_id']); ?>', '<?php echo htmlspecialchars(addslashes($candidate['image_url'])); ?>')">Edit</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div id="editForm" style="display:none;">
        <h3>Edit Candidate</h3>
        <form id="editCandidateForm">
            <input type="hidden" name="candidate_id" id="edit_candidate_id">
            <div class="form-group">
                <label for="edit_name">Name:</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="edit_position_id">Position:</label>
                <select name="position_id" id="edit_position_id" class="form-control" required>
                    <?php foreach ($positions as $position): ?>
                        <option value="<?php echo $position['id']; ?>"><?php echo htmlspecialchars($position['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="edit_image_url">Image URL:</label>
                <input type="text" name="image_url" id="edit_image_url" class="form-control">
            </div>
            <div class="form-group">
                <input type="submit" name="edit_candidate" class="btn btn-primary" value="Update">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editForm').style.display = 'none';">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
function editCandidate(id, name, position_id, image_url) {
    $('#edit_candidate_id').val(id);
    $('#edit_name').val(name);
    $('#edit_position_id').val(position_id);
    $('#edit_image_url').val(image_url);
    $('#editForm').show();
}

function showNotification(message) {
    alert(message);
}

$(document).ready(function() {
    $('.delete-candidate').click(function() {
        var candidate_id = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: 'manage_candidates.php',
            data: {
                candidate_id: candidate_id,
                delete_candidate: true
            },
            success: function(response) {
                showNotification('Candidate deleted successfully');
                $('#candidate-' + candidate_id).remove();
            },
            error: function() {
                showNotification('Error deleting candidate');
            }
        });
    });

    $('#editCandidateForm').submit(function(e) {
        e.preventDefault();
        var candidate_id = $('#edit_candidate_id').val();
        var name = $('#edit_name').val();
        var position_id = $('#edit_position_id').val();
        var image_url = $('#edit_image_url').val();
        
        $.ajax({
            type: 'POST',
            url: 'manage_candidates.php',
            data: {
                candidate_id: candidate_id,
                name: name,
                position_id: position_id,
                image_url: image_url,
                edit_candidate: true
            },
            success: function(response) {
                showNotification('Candidate updated successfully');
                var row = $('#candidate-' + candidate_id);
                row.find('.candidate-name').text(name);
                row.find('.candidate-position').text($('#edit_position_id option:selected').text());
                row.find('.candidate-image-url').text(image_url);
                $('#editForm').hide();
            },
            error: function() {
                showNotification('Error updating candidate');
            }
        });
    });
});
</script>
</body>
</html>
