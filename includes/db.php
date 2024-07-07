<?php
$servername = "localhost";
$username = "u729944332_evote";
$password = "0W/nPa=xN*a";
$database = "u729944332_evote";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
