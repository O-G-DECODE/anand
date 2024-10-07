<?php
// Establish a connection to the MySQL database
$conn = mysqli_connect("localhost", "root", "", "mydatabase");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get current date (YYYY-MM-DD)
$currentDate = date('Y-m-d');
?>
