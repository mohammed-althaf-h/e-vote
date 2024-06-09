<?php
include '../includes/db.php';

// Handle delete candidate request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_candidate'])) {
    $candidate_id = $_POST['candidate_id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get the candidate's register number
        $sql = "SELECT u.registerno FROM users u 
                INNER JOIN candidates c ON u.registerno = c.registerno 
                WHERE c.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $candidate_id);
        $stmt->execute();
        $stmt->bind_result($registerno);
        $stmt->fetch();
        $stmt->close();

        // Delete from candidates table
        $sql = "DELETE FROM candidates WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $candidate_id);
        $stmt->execute();
        $stmt->close();

        // Delete from users table
        $sql = "DELETE FROM users WHERE registerno = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $registerno);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

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

// Handle regenerate password request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['regenerate_password'])) {
    $candidate_id = $_POST['candidate_id'];

    // Generate a new random password
    $password = bin2hex(random_bytes(4)); // Generate a random 8-character password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Update the user's password in the database
    $sql = "UPDATE users SET password = ? WHERE registerno = (SELECT registerno FROM candidates WHERE id = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hashed_password, $candidate_id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true, 'password' => $password]);
    exit();
}

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
    <div id="notification" class="alert alert-success" style="display: none;"></div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Candidate Number</th>
                <th>Name</th>
                <th>Position</th>
                <th>Image URL</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($candidates as $candidate): ?>
            <tr id="candidate-<?php echo htmlspecialchars($candidate['id']); ?>">
                <td class="candidate-registerno"><?php echo htmlspecialchars($candidate['registerno']); ?></td>
                <td class="candidate-name"><?php echo htmlspecialchars($candidate['name']); ?></td>
                <td class="candidate-position"><?php echo htmlspecialchars($candidate['position_name']); ?></td>
                <td class="candidate-image-url"><?php echo htmlspecialchars($candidate['image_url']); ?></td>
                <td>
                    <button class="btn btn-danger delete-candidate" data-id="<?php echo htmlspecialchars($candidate['id']); ?>">Delete</button>
                    <button class="btn btn-primary" onclick="editCandidate('<?php echo htmlspecialchars($candidate['id']); ?>', '<?php echo htmlspecialchars(addslashes($candidate['name'])); ?>', '<?php echo htmlspecialchars($candidate['position_id']); ?>', '<?php echo htmlspecialchars(addslashes($candidate['image_url'])); ?>')">Edit</button>
                    <button class="btn btn-warning regenerate-password" data-id="<?php echo htmlspecialchars($candidate['id']); ?>">Regenerate Password</button>
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

function showNotification(message, type = 'success') {
    var notification = $('#notification');
    notification.removeClass('alert-success alert-danger').addClass('alert-' + type);
    notification.text(message).show();
    setTimeout(function() {
        notification.fadeOut();
    }, 3000);
}

$(document).ready(function() {
    $('.delete-candidate').click(function() {
        var candidate_id = $(this).data('id');
        if (confirm('Are you sure you want to delete this candidate?')) {
            $.ajax({
                type: 'POST',
                url: 'manage_candidates.php',
                data: {
                    candidate_id: candidate_id,
                    delete_candidate: true
                },
                success: function(response) {
                    var res = JSON.parse(response);
                    if (res.success) {
                        showNotification('Candidate deleted successfully');
                        $('#candidate-' + candidate_id).remove();
                    } else {
                        showNotification('Error deleting candidate: ' + res.error, 'danger');
                    }
                },
                error: function() {
                    showNotification('Error deleting candidate', 'danger');
                }
            });
        }
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
                showNotification('Error updating candidate', 'danger');
            }
        });
    });

    $('.regenerate-password').click(function() {
        var candidate_id = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: 'manage_candidates.php',
            data: {
                candidate_id: candidate_id,
                regenerate_password: true
            },
            success: function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    showNotification('Password regenerated successfully. New Password: ' + res.password);
                } else {
                    showNotification('Error regenerating password', 'danger');
                }
            },
            error: function() {
                showNotification('Error regenerating password', 'danger');
            }
        });
    });
});
</script>
</body>
</html>
