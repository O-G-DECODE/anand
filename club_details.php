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

<h1>Club Details</h1>

<?php
if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Club ID</th>
                <th>Club Name</th>
                <th>Action</th>  <!-- New column for Action -->
            </tr>";
    
    while($row = $result->fetch_assoc()) {
        $club_id = htmlspecialchars($row['club_id']);  // Get the club_id for the edit link
        echo "<tr>
                <td>" . htmlspecialchars($row['club_id']) . "</td>
                <td>" . htmlspecialchars($row['club_name']) . "</td>
                <td>
                    <form method='get' action='edit_club.php' style='display:inline;'>  <!-- Edit form -->
                        <input type='hidden' name='club_id' value='$club_id'>
                        <button type='submit' class='btn-edit'>Edit</button>
                    </form>
                </td>
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
