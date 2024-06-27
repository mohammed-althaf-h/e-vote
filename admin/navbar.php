<?php
// Check if a session is already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include '../includes/db.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['registerno'])) {
    header("Location: ../auth/login.php");
    exit();
}

$registerno = $_SESSION['registerno'];
$is_admin = false;

$admin_check_sql = "SELECT isadmin FROM users WHERE registerno = ?";
$admin_check_stmt = $conn->prepare($admin_check_sql);
$admin_check_stmt->bind_param("s", $registerno);
$admin_check_stmt->execute();
$admin_check_result = $admin_check_stmt->get_result();
$admin_row = $admin_check_result->fetch_assoc();

if ($admin_row && $admin_row['isadmin'] == 1) {
    $is_admin = true;
}
$admin_check_stmt->close();
?>

<nav class="nav">
    <div>
        <a href="../home.php" class="nav_logo">
            <i class='bx bx-layer nav_logo-icon'></i>
            <span class="nav_logo-name">My Admin</span>
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
            <a href="#" class="nav_link" onclick="showTab('tab-positions')">
                <i class='bx bx-plus-circle nav_icon'></i>
                <span class="nav_name">Add Position</span>
            </a>
            <a href="../logout.php" class="nav_link">
                <i class="bx bx-log-out nav_icon"></i>
                <span class="nav_name">SignOut</span>
            </a>
        </div>
    </div>
</nav>
