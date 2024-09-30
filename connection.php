<?php
// Establish a connection to the MySQL database
$conn = mysqli_connect("localhost", "root", "", "mydatabase");
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
