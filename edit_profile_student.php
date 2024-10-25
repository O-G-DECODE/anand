<?php
// Start the session
session_start();

// Include the connection file
include 'connection.php';

// Get the roll number from the session
$roll_number = $_SESSION['roll_number'];

// SQL query to fetch student details for the logged-in student
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
            department d ON c.department_id = d.department_id
        WHERE 
            s.roll_number = ?";  // Use a prepared statement

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $roll_number);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <link rel="stylesheet" href="style_of_ussers.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #e0e0e0;
            text-align: left;
        }
        th {
            background-color: #6e8efb;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f8f9ff;
        }
        tr:hover {
            background-color: #e6e9ff;
        }
    </style>
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
                <th>Action</th>  <!-- New column for Action -->
            </tr>";
    
    while($row = $result->fetch_assoc()) {
        $roll_number = htmlspecialchars($row['roll_number']);
        echo "<tr>
                <td>" . htmlspecialchars($row['roll_number']) . "</td>
                <td>" . htmlspecialchars($row['student_name']) . "</td>
                <td>" . htmlspecialchars($row['course_name']) . "</td>
                <td>" . htmlspecialchars($row['department_name']) . "</td>
                <td>
                    <form method='get' action='edit_student.php' style='display:inline;'>
                        <input type='hidden' name='roll_number' value='$roll_number'>
                        <button type='submit' class='btn-edit'>Edit</button>
                    </form>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No results found.</p>";
}

$stmt->close();
$conn->close();
?>

</body>
</html>
