<?php
// Include the connection file
include 'connection.php';

// SQL query to fetch department details
$sql = "SELECT 
            department_id,
            name AS department_name
        FROM 
            department";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Details</title>
    <link rel="stylesheet" href="style_of_ussers.css">
</head>
<body>

<h1>Department Details</h1>

<?php
if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Department ID</th>
                <th>Department Name</th>
            </tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['department_id']) . "</td>
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
