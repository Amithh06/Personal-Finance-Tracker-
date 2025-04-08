<?php
$servername = "localhost"; // Change if using a different server
$username = "root";        // Your database username
$password = "";            // Your database password
$dbname = "cinematic_login";      // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
