<?php
// Establish a connection to the MySQL server
$conn = mysqli_connect("localhost", "root", "");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the database exists
$db_exists = mysqli_query($conn, "SHOW DATABASES LIKE 'mydb'");

if (mysqli_num_rows($db_exists) == 0) {
    // Database does not exist, create it
    $sql = "CREATE DATABASE mydb";
    if (mysqli_query($conn, $sql)) {
        echo "Database created successfully";
    } else {
        die("Error creating database: " . mysqli_error($conn));
    }
}

// Select the database
mysqli_select_db($conn, "mydb");

// Check if the 'club' table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'club'");
if (mysqli_num_rows($table_exists) == 0) {
    // Table does not exist, create it
    $sql = "CREATE TABLE club (
        club_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(60) NOT NULL
    )";
    if (!mysqli_query($conn, $sql)) {
        die("Error creating table 'club': " . mysqli_error($conn));
    }
}

// Check if the 'course' table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'course'");
if (mysqli_num_rows($table_exists) == 0) {
    // Table does not exist, create it
    $sql = "CREATE TABLE course (
        course_id INT AUTO_INCREMENT PRIMARY KEY,
        department_id INT NOT NULL,
        name VARCHAR(40) NOT NULL
    )";
    if (!mysqli_query($conn, $sql)) {
        die("Error creating table 'course': " . mysqli_error($conn));
    }
}

// Check if the 'department' table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'department'");
if (mysqli_num_rows($table_exists) == 0) {
    $sql = "CREATE TABLE department (
        department_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(60) NOT NULL
    )";
    if (!mysqli_query($conn, $sql)) {
        die("Error creating table 'department': " . mysqli_error($conn));
    }
}

// Check if the 'staff' table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'staff'");
if (mysqli_num_rows($table_exists) == 0) {
    $sql = "CREATE TABLE staff (
        staff_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(40) NOT NULL,
        department_id INT NOT NULL,
        email VARCHAR(50) NOT NULL,
        password VARCHAR(50) NOT NULL,
        club_id INT NULL,
        FOREIGN KEY (department_id) REFERENCES department(department_id),
        FOREIGN KEY (club_id) REFERENCES club(club_id)
    )";
    if (!mysqli_query($conn, $sql)) {
        die("Error creating table 'staff': " . mysqli_error($conn));
    }
}

// Check if the 'event' table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'event'");
if (mysqli_num_rows($table_exists) == 0) {
    $sql = "CREATE TABLE event (
        event_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(60) NOT NULL,
        date DATE NOT NULL,
        period VARCHAR(20) NOT NULL,
        staff_id INT NOT NULL,
        create_date DATE NOT NULL,
        FOREIGN KEY (staff_id) REFERENCES staff(staff_id)
    )";
    if (!mysqli_query($conn, $sql)) {
        die("Error creating table 'event': " . mysqli_error($conn));
    }
}

// Check if the 'request' table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'request'");
if (mysqli_num_rows($table_exists) == 0) {
    $sql = "CREATE TABLE request (
        roll_number INT NOT NULL,
        event_id INT NOT NULL,
        approve INT NULL,
        date_id INT NOT NULL
       
    )";
    if (!mysqli_query($conn, $sql)) {
        die("Error creating table 'request': " . mysqli_error($conn));
    }
}

// Check if the 'student' table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'student'");
if (mysqli_num_rows($table_exists) == 0) {
    $sql = "CREATE TABLE student (
        roll_number BIGINT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL,
        course_id INT NOT NULL,
        club_id INT NULL
    )";
    if (!mysqli_query($conn, $sql)) {
        die("Error creating table 'student': " . mysqli_error($conn));
    }
}

// Check if the 'day' table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'day'");
if (mysqli_num_rows($table_exists) == 0) {
    $sql = "CREATE TABLE day (
        date_id INT AUTO_INCREMENT PRIMARY KEY,
        date DATE NOT NULL,
        type INT NOT NULL,
        event_id INT NOT NULL
    )";
    if (!mysqli_query($conn, $sql)) {
        die("Error creating table 'day': " . mysqli_error($conn));
    }
}

// Check if the 'admin' table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'admin'");
if (mysqli_num_rows($table_exists) == 0) {
    $sql = "CREATE TABLE admin (
        staff_id INT PRIMARY KEY
    )";
    if (!mysqli_query($conn, $sql)) {
        die("Error creating table 'admin': " . mysqli_error($conn));
    }
}

// Continue with your database operations
?>
