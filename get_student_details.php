<?php
include("connection.php");

if (isset($_GET['roll_number'])) {
    $roll_number = $_GET['roll_number']; // added closing single quote
    $stmt = $conn->prepare("SELECT roll_number, name, course_id, year_id FROM student WHERE roll_number = ?");
    $stmt->bind_param("s", $roll_number);
    $stmt->execute();
    $stmt->bind_result($roll_number, $name, $course_id, $year_id); // corrected bind_result
    $stmt->fetch();
    $stmt->close();

    $response = [
        'roll_number' => $roll_number,
        'name' => $name,
        'course' => getCourseName($course_id, $conn),
        'year' => getYear($year_id, $conn)
    ];

    echo json_encode($response);
}
?>