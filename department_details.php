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

<h1>Department Details</h1>

<?php
if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Department ID</th>
                <th>Department Name</th>
                <th>Action</th>  <!-- New column for Action -->
            </tr>";
    
    while($row = $result->fetch_assoc()) {
        $department_id = htmlspecialchars($row['department_id']);  // Get the department_id for the edit link
        echo "<tr>
                <td>" . htmlspecialchars($row['department_id']) . "</td>
                <td>" . htmlspecialchars($row['department_name']) . "</td>
                <td>
                    <form method='get' action='edit_department.php' style='display:inline;'>  <!-- Edit form -->
                        <input type='hidden' name='department_id' value='$department_id'>
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
