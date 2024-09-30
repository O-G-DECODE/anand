<?php
// Include the connection file
include 'connection.php';

// SQL query to fetch course details
$sql = "SELECT 
            course_id,
            name AS course_name
        FROM 
            course";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details</title>
    <link rel="stylesheet" href="style_of_ussers.css">
</head>
<body>

<h1>Course Details</h1>

<?php
if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Course ID</th>
                <th>Course Name</th>
            </tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['course_id']) . "</td>
                <td>" . htmlspecialchars($row['course_name']) . "</td>
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
