<?php
// Establish a connection to the MySQL database
$conn = mysqli_connect("localhost", "root", "", "mydb");
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
