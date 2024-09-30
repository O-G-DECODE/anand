<?php
// Include the connection file
include 'connection.php';

// SQL query to fetch student details along with course and department names
$sql = "SELECT 
            s.roll_number,
            s.name AS student_name,
            c.name AS course_name,
            d.name AS department_name
        FROM 
            student s
        JOIN 
            course c ON s.course_id = c.course_id
        JOIN 
            department d ON c.department_id = d.department_id";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <link rel="stylesheet" href="style_of_ussers.css">
</head>
<body>

<h1>Student Details</h1>

<?php
if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Roll Number</th>
                <th>Name</th>
                <th>Course</th>
                <th>Department</th>
            </tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['roll_number']) . "</td>
                <td>" . htmlspecialchars($row['student_name']) . "</td>
                <td>" . htmlspecialchars($row['course_name']) . "</td>
                <td>" . htmlspecialchars($row['department_name']) . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No results found.</p>";
}

$conn->close();
?>

</body>
</html>
