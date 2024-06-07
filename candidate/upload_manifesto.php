<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session at the beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'header.php';
// include '../includes/csrf.php'; // Ensure this path is correct

// Process form submission for manifesto upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_manifesto'])) {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }

    // Handle file upload
    if ($_FILES['manifesto_file']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../uploads/manifestos/";
        $target_file = $target_dir . basename($_FILES["manifesto_file"]["name"]);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check file type
        if ($file_type != "pdf") {
            echo "Only PDF files are allowed.";
        } else {
            if (move_uploaded_file($_FILES["manifesto_file"]["tmp_name"], $target_file)) {
                // Save the manifesto file path to the database
                $sql = "UPDATE candidates SET manifesto = ? WHERE registerno = ?";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    echo "Prepare statement failed: " . $conn->error;
                    exit;
                }
                $stmt->bind_param("ss", $target_file, $_SESSION['registerno']);
                if ($stmt->execute()) {
                    echo "The file " . htmlspecialchars(basename($_FILES["manifesto_file"]["name"])) . " has been uploaded.";
                } else {
                    echo "Execute statement failed: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "File upload error: " . $_FILES['manifesto_file']['error'];
    }
}
?>

<div class="container" id="tab-upload">
    <h1>Upload Manifesto</h1>
    <form action="upload_manifesto.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
        <div class="form-group">
            <label for="manifesto_file">Upload Manifesto (PDF only):</label>
            <input type="file" name="manifesto_file" id="manifesto_file" accept=".pdf" required>
        </div>
        <div class="form-group">
            <button class="btn" type="submit" name="upload_manifesto">Upload Manifesto</button>
        </div>
    </form>
</div>

</body>
</html>
