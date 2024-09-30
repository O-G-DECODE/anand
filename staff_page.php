<?php
// Include the connection file
include 'connection.php';

// SQL query to fetch staff details along with department names
$sql = "SELECT 
            s.name AS staff_name,
            d.name AS department_name,
            s.email
        FROM 
            staff s
        JOIN 
            department d ON s.department_id = d.department_id";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Details</title>
    <link rel="stylesheet" href="style_of_ussers.css">
</head>
<body>

<h1>Staff Details</h1>

<?php
if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Name</th>
                <th>Department</th>
                <th>Email</th>
            </tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['staff_name']) . "</td>
                <td>" . htmlspecialchars($row['department_name']) . "</td>
                <td>" . htmlspecialchars($row['email']) . "</td>
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
