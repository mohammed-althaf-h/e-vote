<?php
// Ensure session is started only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'header.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['registerno']) || $_SESSION['isadmin'] != 1) {
    header("Location:../auth/login.php");
    exit();
}

// Include database connection
include '../includes/db.php';

// Fetch current voting status
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'get_voting_status') {
    $sql = "SELECT voting_enabled FROM settings WHERE id = 1";
    $result = $conn->query($sql);
    $voting_enabled = false;
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $voting_enabled = $row['voting_enabled'];
    }
    echo json_encode(['voting_enabled' => $voting_enabled]);
    exit();
}

// Toggle voting status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'toggle_voting') {
    $sql = "SELECT voting_enabled FROM settings WHERE id = 1";
    $result = $conn->query($sql);
    $voting_enabled = false;
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $voting_enabled = $row['voting_enabled'];
    }
    $new_status = $voting_enabled ? 0 : 1;

    $sql = "UPDATE settings SET voting_enabled = ? WHERE id = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $new_status);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'new_status' => $new_status]);
    } else {
        echo json_encode(['success' => false]);
    }
    $stmt->close();
    exit();
}

// Fetch voting results
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'get_voting_results') {
    $sql = "SELECT positions.id AS position_id, positions.name AS position_name, candidates.name AS candidate_name, COUNT(votes.id) AS vote_count
            FROM positions
            LEFT JOIN candidates ON positions.id = candidates.position_id
            LEFT JOIN votes ON candidates.id = votes.candidate_id
            GROUP BY positions.id, candidates.id
            ORDER BY positions.id, vote_count DESC";
    $result = $conn->query($sql);

    $position_data = [];
    while ($row = $result->fetch_assoc()) {
        $position_name = $row['position_name'];
        if (!isset($position_data[$position_name])) {
            $position_data[$position_name] = [
                'candidate_names' => [],
                'vote_counts' => []
            ];
        }
        if ($row['candidate_name']) {
            $position_data[$position_name]['candidate_names'][] = $row['candidate_name'];
            $position_data[$position_name]['vote_counts'][] = $row['vote_count'];
        }
    }
    echo json_encode($position_data);
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .chart-container {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Admin Dashboard</h2>
        <button id="toggle-voting" class="btn btn-primary"></button>
        <br><br><br><br><br>
        <h3>Voting Results:</h3>
        <div id="voting-results" class="chart-grid"></div>
    </div>

    <!-- Modal for notifications -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Notification</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="notificationMessage"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/blueimp-md5/js/md5.min.js"></script>
    <script>
    $(document).ready(function() {
        function updateVotingButton(status) {
            $('#toggle-voting').text(status ? 'Disable Voting' : 'Enable Voting');
        }

        function showNotification(message) {
            $('#notificationMessage').text(message);
            $('#notificationModal').modal('show');
        }

        function fetchVotingStatus() {
            $.ajax({
                type: 'GET',
                url: 'dashboard.php',
                data: { action: 'get_voting_status' },
                success: function(response) {
                    var res = JSON.parse(response);
                    updateVotingButton(res.voting_enabled);
                },
                error: function() {
                    alert('Error fetching voting status');
                }
            });
        }

        function toggleVotingStatus() {
            $.ajax({
                type: 'POST',
                url: 'dashboard.php',
                data: { action: 'toggle_voting' },
                success: function(response) {
                    var res = JSON.parse(response);
                    if (res.success) {
                        updateVotingButton(res.new_status);
                        showNotification('Voting status changed successfully!');
                    } else {
                        showNotification('Error toggling voting status');
                    }
                },
                error: function() {
                    showNotification('Error toggling voting status');
                }
            });
        }

        function fetchVotingResults() {
            $.ajax({
                type: 'GET',
                url: 'dashboard.php',
                data: { action: 'get_voting_results' },
                success: function(response) {
                    var res = JSON.parse(response);
                    $('#voting-results').empty();
                    if ($.isEmptyObject(res)) {
                        $('#voting-results').append('<div class="chart-container"><h4>No data available right now</h4></div>');
                        return;
                    }
                    for (var position_name in res) {
                        var data = res[position_name];
                        var totalVotes = data.vote_counts.reduce((a, b) => a + b, 0);
                        var chartId = 'myChart-' + md5(position_name);
                        $('#voting-results').append(
                            '<div class="chart-container">' +
                            '<h4>' + position_name + '</h4>' +
                            '<canvas id="' + chartId + '" style="width:100%;height:300px;"></canvas>' +
                            '</div>'
                        );
                        var ctx = document.getElementById(chartId).getContext('2d');
                        if (totalVotes === 0 || data.candidate_names.length === 0) {
                            new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: ['No data available'],
                                    datasets: [{
                                        data: [1],
                                        backgroundColor: ['rgba(211, 211, 211, 0.5)'],
                                        borderColor: ['rgba(211, 211, 211, 1)'],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    plugins: {
                                        tooltip: {
                                            callbacks: {
                                                label: function() {
                                                    return 'No data available';
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        } else {
                            new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: data.candidate_names,
                                    datasets: [{
                                        data: data.vote_counts,
                                        backgroundColor: [
                                            'rgba(255, 99, 132, 0.2)',
                                            'rgba(54, 162, 235, 0.2)',
                                            'rgba(255, 206, 86, 0.2)',
                                            'rgba(75, 192, 192, 0.2)',
                                            'rgba(153, 102, 255, 0.2)',
                                            'rgba(255, 159, 64, 0.2)'
                                        ],
                                        borderColor: [
                                            'rgba(255, 99, 132, 1)',
                                            'rgba(54, 162, 235, 1)',
                                            'rgba(255, 206, 86, 1)',
                                            'rgba(75, 192, 192, 1)',
                                            'rgba(153, 102, 255, 1)',
                                            'rgba(255, 159, 64, 1)'
                                        ],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(tooltipItem) {
                                                    var label = tooltipItem.label || '';
                                                    var value = tooltipItem.raw || 0;
                                                    var percentage = (value / totalVotes * 100).toFixed(2);
                                                    return label + ': ' + value + ' votes (' + percentage + '%)';
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    }
                },
                error: function() {
                    showNotification('Error fetching voting results');
                }
            });
        }

        $('#toggle-voting').click(toggleVotingStatus);
        fetchVotingStatus();
        fetchVotingResults();
    });
    </script>
</body>
</html>
