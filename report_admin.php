<?php
include("connection.php"); // Database connection
session_start(); // Start the session

// Check if the admin is logged in
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Fetch the admin's staff_id from the staff table (if required)
    $stmt = $conn->prepare("SELECT staff_id FROM staff WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($staff_id);
        $stmt->fetch();
        $stmt->close();

        // Store staff_id in the session
        $_SESSION['staff_id'] = $staff_id; // Store staff_id in session

    } else {
        echo "Error fetching staff information.";
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
    <title>Admin Reports Page</title>
   <link rel="stylesheet" href="report_page_style.css">
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
        <h2>Admin Reports Page</h2>

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

        <!-- Club Report (Select Club) -->
        <div class="form-group">
            <form action="club_report_admin.php" method="post">
                <label for="club_id">Club Report (Select Club)</label>
                <select id="club_id" name="club_id">
                    <?php
                    // Fetch club names from the club table
                    $stmt = $conn->prepare("SELECT club_id, name FROM club");
                    if ($stmt) {
                        $stmt->execute();
                        $result = $stmt->get_result();

                        while ($row = $result->fetch_assoc()) {
                            $club_id = $row['club_id'];
                            $club_name = $row['name'];
                            echo "<option value='" . htmlspecialchars($club_id) . "'>" . htmlspecialchars($club_name) . "</option>";
                        }
                        $stmt->close();
                    }
                    ?>
                </select>
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" required>
                <label for="end_date">End Date</label>
                <input type="date" id="end_date" name="end_date" required>
                <button type="submit">Generate Report</button>
            </form>
        </div>

        <!-- Event Report -->
        <div class="form-group">
            <label for="event_name">Event Report (Select Event Name)</label>
            <form action="event_report.php" method="post">
                <select id="event_name" name="event_id"> <!-- Change to 'event_id' -->
                <?php
                // Fetch event IDs and names from the event table where there are approved requests
                $stmt = $conn->prepare("
                    SELECT e.event_id, e.name 
                    FROM event e
                    JOIN request r ON e.event_id = r.event_id
                    WHERE r.approve > 0
                    GROUP BY e.event_id
                ");
                if ($stmt) {
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()) {
                        $event_id = $row['event_id'];
                        $event_name = $row['name'];
                        echo "<option value='" . htmlspecialchars($event_id) . "'>" . htmlspecialchars($event_name) . "</option>";
                    }
                    $stmt->close();
                }
                ?>

                </select>
                <button type="submit">Generate Report</button>
            </form>
        </div>
    </div>
</body>
</html>
