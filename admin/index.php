<?php
session_start();

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

if (!$is_admin) {
    header("Location: ./index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.1.js"></script>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap");
        :root {
            --header-height: 3rem;
            --nav-width: 68px;
            --first-color: black;
            --first-color-light: #AFA5D9;
            --white-color: #F7F6FB;
            --body-font: 'Nunito', sans-serif;
            --normal-font-size: 1rem;
            --z-fixed: 100;
        }
        *,::before,::after {
            box-sizing: border-box;
        }
        body {
            position: relative;
            margin: var(--header-height) 0 0 0;
            padding: 0 1rem;
            font-family: var(--body-font);
            font-size: var(--normal-font-size);
            transition: .5s;
        }
        a {
            text-decoration: none;
        }
        .header {
            width: 100%;
            height: var(--header-height);
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            background-color: black;
            z-index: var(--z-fixed);
            transition: .5s;
        }
        .header_toggle {
            color: var(--white-color);
            font-size: 1.5rem;
            cursor: pointer;
        }
        .header_img {
            width: 35px;
            height: 35px;
            display: flex;
            justify-content: center;
            border-radius: 50%;
            overflow: hidden;
        }
        .header_img img {
            width: 40px;
        }
        .l-navbar {
            position: fixed;
            top: 0;
            left: -30%;
            width: var(--nav-width);
            height: 100vh;
            background-color: var(--first-color);
            padding: .5rem 1rem 0 0;
            transition: .5s;
            z-index: var(--z-fixed);
        }
        .nav {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }
        .nav_logo, .nav_link {
            display: grid;
            grid-template-columns: max-content max-content;
            align-items: center;
            column-gap: 1rem;
            padding: .5rem 0 .5rem 1.5rem;
        }
        .nav_logo {
            margin-bottom: 2rem;
        }
        .nav_logo-icon {
            font-size: 1.25rem;
            color: var(--white-color);
        }
        .nav_logo-name {
            color: var(--white-color);
            font-weight: 700;
        }
        .nav_link {
            position: relative;
            color: var(--first-color-light);
            margin-bottom: 1.5rem;
            transition: .3s;
        }
        .nav_link:hover {
            color: var(--white-color);
        }
        .nav_icon {
            font-size: 1.25rem;
        }
        .show {
            left: 0;
        }
        .body-pd {
            padding-left: calc(var(--nav-width) + 1rem);
        }
        .active {
            color: var(--white-color);
        }
        .active::before {
            content: '';
            position: absolute;
            left: 0;
            width: 2px;
            height: 32px;
            background-color: var(--white-color);
        }
        .height-100 {
            height: 100vh;
        }
        @media screen and (min-width: 768px) {
            body {
                margin: calc(var(--header-height) + 1rem) 0 0 0;
                padding-left: calc(var(--nav-width) + 2rem);
            }
            .header {
                height: calc(var(--header-height) + 1rem);
                padding: 0 2rem 0 calc(var(--nav-width) + 2rem);
            }
            .header_img {
                width: 40px;
                height: 40px;
            }
            .header_img img {
                width: 45px;
            }
            .l-navbar {
                left: 0;
                padding: 1rem 1rem 0 0;
            }
            .show {
                width: calc(var(--nav-width) + 156px);
            }
            .body-pd {
                padding-left: calc(var(--nav-width) + 188px);
            }
            .h4 {
                z-index: -999;
                color: var(--white-color);
            }
        }
        .notification {
            position: fixed;
            top: 3rem; /* Below the header */
            left: 4rem;
            z-index: -999; /* Make sure it's above other elements */
            display: none;
            min-width: 300px;
        }
        .alert{
            z-index: 1000;
        }
    </style>
</head>
<body id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
    </header>
    <div id="container">
        <div class="content">
            <!-- Notification Area -->
            <div id="notification" class="notification alert"></div>
            <!-- DASHBOARD -->
            <div id="tab-dashboard" class="tab">
                <div class="height-100 bg-light">
                    <h4>Dashboard</h4>
                    <?php include 'dashboard.php'; ?>
                </div>
            </div>
            <!-- DATABASE -->
            <div id="tab-data" class="tab" style="display: none;">
                <div class="height-100 bg-light">
                    <h4>Database</h4>
                    <?php include 'add_candidate.php'; ?>
                </div>
            </div>
            <!-- ADVERTISEMENTS -->
            <div id="tab-ad" class="tab" style="display: none;">
                <div class="height-100 bg-light">
                    <h4>Advertisements</h4>
                    <div id="manage-candidates"></div>
                </div>
            </div>
            <!-- POSITIONS -->
            <div id="tab-positions" class="tab" style="display: none;">
                <div class="height-100 bg-light">
                    <h4>Positions</h4>
                    <?php include 'add_position.php'; ?>
                </div>
            </div>
            <!-- TOOLS -->
            <div id="tab-tools" class="tab" style="display: none;">
                <div class="height-100 bg-light">
                    <h4>Tools</h4>
                    <?php include 'view_users.php'; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="l-navbar" id="nav-bar">
        <?php include 'navbar.php'; ?>
    </div>
    <!-- JS RESOURCES -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            const showNavbar = (toggleId, navId, bodyId, headerId) => {
                const toggle = document.getElementById(toggleId),
                      nav = document.getElementById(navId),
                      bodypd = document.getElementById(bodyId),
                      headerpd = document.getElementById(headerId);

                // Validate that all variables exist
                if (toggle && nav && bodypd && headerpd) {
                    toggle.addEventListener('click', () => {
                        nav.classList.toggle('show');
                        toggle.classList.toggle('bx-x');
                        bodypd.classList.toggle('body-pd');
                        headerpd.classList.toggle('body-pd');
                    });
                }
            }

            showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header');

            /*===== LINK ACTIVE =====*/
            const linkColor = document.querySelectorAll('.nav_link');

            function colorLink() {
                if (linkColor) {
                    linkColor.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                }
            }
            linkColor.forEach(l => l.addEventListener('click', colorLink));
        });

        // Function to show the selected tab
        function showTab(tabId) {
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.style.display = 'none';
            });
            document.getElementById(tabId).style.display = 'block';
                        // Fetch latest candidates if the manage candidates tab is selected
            if (tabId === 'tab-ad') {
                loadManageCandidates();
            }
        }

      // Function to load Manage Candidates content
        function loadManageCandidates() {
            $.ajax({
                url: 'manage_candidates.php',
                method: 'GET',
                success: function(data) {
                    $('#tab-ad').html(data);
                    initializeManageCandidates();
                },
                error: function() {
                    showNotification('Error loading candidates', 'danger');
                }
            });
        }

        // Function to initialize Manage Candidates functionalities
        function initializeManageCandidates() {
            // Existing code to initialize manage candidates functionalities
        }

        // Function to show notification
        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.className = 'notification alert alert-' + type;
            notification.textContent = message;
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }
        document.addEventListener('DOMContentLoaded', function () {
            // Check if the tab-ad div should be displayed
            const tabAd = document.getElementById('tab-ad');
            if (tabAd) {
                // Fetch the content of manage_candidates.php
                fetch('manage_candidates.php')
                    .then(response => response.text())
                    .then(data => {
                        // Insert the fetched content into the manage-candidates div
                        document.getElementById('manage-candidates').innerHTML = data;
                        // Display the tab-ad div
                        tabAd.style.display = 'block';
                    })
                    .catch(error => console.error('Error fetching the content:', error));
            }
        });
    </script>
</body>
</html>
