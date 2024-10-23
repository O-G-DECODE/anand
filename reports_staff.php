<?php
include("connection.php"); // Database connection
session_start(); // Start the session

// Check if the staff is logged in
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

// Fetch the staff's club_id from the staff table
$stmt = $conn->prepare("SELECT club_id, staff_id FROM staff WHERE email = ?");
if ($stmt) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($club_id, $staff_id); // Bind both club_id and staff_id
    $stmt->fetch();
    $stmt->close();

    // Store club_id and staff_id in the session
    $_SESSION['club_id'] = $club_id;
    $_SESSION['staff_id'] = $staff_id; // Store staff_id in session

    // Fetch the club name for display (optional)
    if ($club_id !== null) {
        $stmt = $conn->prepare("SELECT name FROM club WHERE club_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $club_id);
            $stmt->execute();
            $stmt->bind_result($club_name);
            $stmt->fetch();
            $stmt->close();
        }
    } else {
        $club_name = "No club assigned";
    }

} else {
    echo "Error fetching staff club information.";
}
} else {
    echo "No user logged in.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Page</title>
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --accent-color: #fd79a8;
            --background-color: #f9f9f9;
            --text-color: #2d3436;
            --card-background: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #e4d3ea;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 40px;
            background-color: var(--card-background);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: var(--primary-color);
            font-size: 2.5em;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--primary-color);
        }

        select, input[type="text"], input[type="date"], button {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 2px solid var(--secondary-color);
            font-size: 1em;
            transition: all 0.3s ease;
        }

        button {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            margin-top: 20px;
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
    </style>

    <script>
        // Autocomplete function for student report
        function autocomplete(input, data) {
            input.addEventListener("input", function() {
                let value = this.value;
                let dropdown = document.getElementById("autocomplete-list");
                dropdown.innerHTML = "";
                
                if (!value) return;
                data.forEach(item => {
                    if (item.toLowerCase().includes(value.toLowerCase())) {
                        let option = document.createElement("div");
                        option.innerHTML = item;
                        option.addEventListener("click", function() {
                            input.value = item;
                            dropdown.innerHTML = "";
                        });
                        dropdown.appendChild(option);
                    }
                });
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            let studentInput = document.getElementById("student_name");
            let studentNames = <?php
                $student_names = [];
                $stmt = $conn->prepare("SELECT name FROM student");
                if ($stmt) {
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $student_names[] = $row['name'];
                    }
                    $stmt->close();
                }
                echo json_encode($student_names);
            ?>;
            autocomplete(studentInput, studentNames);
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>Generate Reports</h2>

       <!-- Student Report -->
<div class="form-group">
    <label for="student_name">Student Report (Select Student Name)</label>
    <input type="text" id="student_name" name="student_name" placeholder="Start typing student name...">
    <div id="autocomplete-list"></div>
    <form action="student_report_admin.php" method="post">
        <input type="hidden" name="student_name_selected" id="student_name_hidden">
        
        <!-- Add date fields for the date range -->
        <label for="from_date_student">From Date</label>
        <input type="date" id="from_date_student" name="from_date_student">
        
        <label for="to_date_student">To Date</label>
        <input type="date" id="to_date_student" name="to_date_student" style="margin-top: 10px;">
        
        <button type="submit" onclick="document.getElementById('student_name_hidden').value = document.getElementById('student_name').value;">Generate Report</button>
    </form>
</div>

        <!-- Club Report (Uses staff's club_id from session) -->
        <div class="form-group">
    <form action="club_report.php" method="post">
        <label for="from_date">Club Report<?php echo " ( $club_name )"; ?> (Select Date Range)</label>
        <input type="date" id="from_date" name="from_date">
        <input type="date" id="to_date" name="to_date" style="margin-top: 10px;">
        <button type="submit">Generate Report</button>
    </form>
</div>


  <!-- Event Report -->
<div class="form-group">
    <label for="event_name">Event Report (Select Event Name)</label>
    <form action="event_report.php" method="post">
        <select id="event_name" name="event_id"> <!-- Using event_id instead of event_name -->
        <?php
        // Fetch event names and IDs from the event table for the staff member
        $stmt = $conn->prepare("
            SELECT e.event_id, e.name
            FROM event e
            JOIN request r ON e.event_id = r.event_id
            WHERE e.staff_id = ? AND r.approve > 0
            GROUP BY e.event_id
        ");

        if ($stmt) {
            $stmt->bind_param("i", $_SESSION['staff_id']); // Use session's staff_id
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $event_id = $row['event_id'];
                $event_name = $row['name'];

                // Display the event name as an option, use event_id as the value
                echo "<option value='" . htmlspecialchars($event_id) . "'>" . htmlspecialchars($event_name) . "</option>";
            }
            $stmt->close();
        } else {
            echo "<option>No events available</option>";
        }
        ?>
        </select>
        <button type="submit">Generate Report</button>
    </form>
</div>

    </div>
</body>
</html>
