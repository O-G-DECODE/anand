<?php
// Establish a connection to the MySQL server
$conn = mysqli_connect("localhost", "root", "");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if database exists
$db_exists = mysqli_query($conn, "SHOW DATABASES LIKE 'mydb'");

if (mysqli_num_rows($db_exists) == 0) {
    // Database does not exist, create it
    $sql = "CREATE DATABASE mydb";
    if (mysqli_query($conn, $sql)) {
        echo "Database created successfully";
    } else {
        echo "Error creating database: " . mysqli_error($conn);
    }
    // Select the newly created database
    mysqli_select_db($conn, "mydb");
} else {
    // Database exists, select it
    mysqli_select_db($conn, "mydb");
}

// Check if table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'club'");

if (mysqli_num_rows($table_exists) == 0) {
    // Table does not exist, create it
    $sql = "CREATE TABLE club (
        club_id INT(12) NOT NULL PRIMARY KEY,
        name TEXT NOT NULL
    )";
    if (mysqli_query($conn, $sql)) {
        echo "Table created successfully";
    } else {
        echo "Error creating table: " . mysqli_error($conn);
    }
}

// Check if table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'course'");

if (mysqli_num_rows($table_exists) == 0) {
    // Table does not exist, create it
    $sql = "CREATE TABLE course (
        course_id INT(12) NOT NULL PRIMARY KEY,
        department_id INT(12) NOT NULL,
        name TEXT(40) NOT NULL
    )";
    if (mysqli_query($conn, $sql)) {
        echo "Table created successfully";
    } else {
        echo "Error creating table: " . mysqli_error($conn);
    }
}

// Check if table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'department'");

if (mysqli_num_rows($table_exists) == 0) {
    // Table does not exist, create it
    $sql = "CREATE TABLE club (
        club_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(60) NOT NULL
    )";
    if (mysqli_query($conn, $sql)) {
        echo "Table created successfully";
    } else {
        echo "Error creating table: " . mysqli_error($conn);
    }
}
 
// Check if table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'staff'");

if (mysqli_num_rows($table_exists) == 0) {
    // Table does not exist, create it
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
    if (mysqli_query($conn, $sql)) {
        echo "Table created successfully";
    } else {
        echo "Error creating table: " . mysqli_error($conn);
    }
}

// Check if table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'event'");

if (mysqli_num_rows($table_exists) == 0) {
    // Table does not exist, create it
    $sql = "CREATE TABLE event (
        event_id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name TEXT(60) NOT NULL,
        date DATE NOT NULL,
        period VARCHAR(20) NOT NULL,
        staff_id INT(10) NOT NULL
    )";
    if (mysqli_query($conn, $sql)) {
        echo "Table created successfully";
    } else {
        echo "Error creating table: " . mysqli_error($conn);
    }
}

// Check if table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'request'");

if (mysqli_num_rows($table_exists) == 0) {
    // Table does not exist, create it
    $sql = "CREATE TABLE request (
        roll_number INT(12) NOT NULL,
        event_id INT(10) NOT NULL,
        approve INT(1)  NULL
    )";
    if (mysqli_query($conn, $sql)) {
        echo "Table created successfully";
    } else {
        echo "Error creating table: " . mysqli_error($conn);
    }
}

// Check if table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'staff'");

if (mysqli_num_rows($table_exists) == 0) {
    // Table does not exist, create it
    $sql = "CREATE TABLE staff (
        staff_id INT(12) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name TEXT(40) NOT NULL,
        email VARCHAR(50) NOT NULL,
        department_id INT(12) NOT NULL,
        password VARCHAR(50) NOT NULL,
        club_id INT(12) NOT NULL
    )";
    if (mysqli_query($conn, $sql)) {
        echo "Table created successfully";
    } else {
        echo "Error creating table: " . mysqli_error($conn);
    }
}

// Check if table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'student'");

if (mysqli_num_rows($table_exists) == 0) {
    // Table does not exist, create it
    $sql = "CREATE TABLE student (
    roll_number BIGINT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    course_id INT NOT NULL,
    club_id INT NOT NULL
)";
    if (mysqli_query($conn, $sql)) {
        echo "Table created successfully";
    } else {
        echo "Error creating table: " . mysqli_error($conn);
    }
}

// Continue with your database operations
?>