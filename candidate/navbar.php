<?php
// Check if a session is already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include '../includes/db.php';

// Check if the user is logged in and is an cand
if (!isset($_SESSION['registerno'])) {
    header("Location: ../auth/login.php");
    exit();
}

$registerno = $_SESSION['registerno'];
$is_cand = false;

$cand_check_sql = "SELECT iscand FROM users WHERE registerno = ?";
$cand_check_stmt = $conn->prepare($cand_check_sql);
$cand_check_stmt->bind_param("s", $registerno);
$cand_check_stmt->execute();
$cand_check_result = $cand_check_stmt->get_result();
$cand_row = $cand_check_result->fetch_assoc();

if ($cand_row && $cand_row['iscand'] == 1) {
    $is_cand = true;
}
$cand_check_stmt->close();
?>

<nav class="nav">
    <div>
        <a href="../index.php" class="nav_logo">
            <i class='bx bx-layer nav_logo-icon'></i>
            <span class="nav_logo-name">My cand</span>
        </a>
        <div class="nav_list">
            <a href="#" class="nav_link active" onclick="showTab('tab-dashboard')">
                <i class='bx bx-grid-alt nav_icon'></i>
                <span class="nav_name">Dashboard</span>
            </a>
            <a href="#" class="nav_link" onclick="showTab('tab-data')">
                <i class='bx bx-folder nav_icon'></i>
                <span class="nav_name">Add Candidates</span>
            </a>
            <a href="#" class="nav_link" onclick="showTab('tab-ad')">
                <i class='bx bx-message-square-detail nav_icon'></i>
                <span class="nav_name">Manage Candidates</span>
            </a>
            <a href="#" class="nav_link" onclick="showTab('tab-tools')">
                <i class='bx bx-wrench nav_icon'></i>
                <span class="nav_name">View Users</span>
            </a>
            <!-- Uncomment this if settings are needed -->
            <!-- <a href="#" class="nav_link" onclick="showTab('tab-settings')">
                <i class='bx bx-cog nav_icon'></i>
                <span class="nav_name">Settings</span>
            </a> -->
			<a href="../logout.php" class="nav_link"> 
			<i class="bx bx-log-out nav_icon">
			</i> <span class="nav_name">SignOut</span> </a>
        </div>
    </div>
</nav>
