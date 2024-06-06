<?php
include 'header.php';

// Process form submission for campaign details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_campaign'])) {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }

    $campaign_details = trim($_POST['campaign_details']);
    $registerno = $_SESSION['registerno'];

    // Save campaign details to the database
    $sql = "UPDATE candidates SET campaign_details = ? WHERE registerno = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $campaign_details, $registerno);
    $stmt->execute();
    $stmt->close();
}
?>

<div class="container" id="tab-campaign">
    <h1>Campaign Details</h1>
    <form action="campaign_details.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
        <div class="form-group">
            <label for="campaign_details">Campaign Details:</label>
            <textarea name="campaign_details" id="campaign_details" rows="5" required><?php echo htmlspecialchars($candidate['campaign_details']); ?></textarea>
        </div>
        <div class="form-group">
            <button class="btn" type="submit" name="submit_campaign">Submit Campaign Details</button>
        </div>
    </form>
</div>

</body>
</html>
