<?php
// Include the connection file
include 'connection.php';

// SQL query to fetch club details
$sql = "SELECT 
            club_id,
            name AS club_name
        FROM 
            club";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Details</title>
    <link rel="stylesheet" href="style_of_ussers.css">
</head>
<body>

<h1>Club Details</h1>

<?php
if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Club ID</th>
                <th>Club Name</th>
            </tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['club_id']) . "</td>
                <td>" . htmlspecialchars($row['club_name']) . "</td>
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
