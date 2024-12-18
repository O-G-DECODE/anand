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

// Fetch student details (roll number, name, course, department) for autocomplete
$student_details = [];
$stmt = $conn->prepare("
    SELECT s.roll_number, s.name, c.name as course_name, d.name as department_name
    FROM student s
    JOIN course c ON s.course_id = c.course_id
    JOIN department d ON c.department_id = d.department_id
");

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $student_details[] = [
            'roll_number' => $row['roll_number'],
            'name' => $row['name'],
            'course_name' => $row['course_name'],
            'department_name' => $row['department_name']
        ];
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Page</title>
    <link rel="stylesheet" href="report_page_style.css">
    <style>
        /* Style for the toggle buttons */
        .btn-container {
            margin-bottom: 20px;
        }
        .toggle-btn {
            padding: 10px 20px;
            margin-right: 10px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .toggle-btn:hover {
            background-color: #45a049;
        }

        /* Initially hide all forms */
        .report-form {
            display: none;
        }

        /* Style for the active report */
        .active-form {
            display: block;
        }
    </style>
    <script>
        // Toggle visibility of the report forms based on the clicked button
        function showReportForm(formId) {
            // Hide all report forms
            var forms = document.querySelectorAll('.report-form');
            forms.forEach(function(form) {
                form.classList.remove('active-form');
            });

            // Show the selected form
            var selectedForm = document.getElementById(formId);
            selectedForm.classList.add('active-form');
        }

        // Autocomplete function for student report
        function autocomplete(input, data) {
            input.addEventListener("input", function() {
                let value = this.value;
                let dropdown = document.getElementById("autocomplete-list");
                dropdown.innerHTML = "";

                if (!value) return;
                data.forEach(item => {
                    if (item.name.toLowerCase().includes(value.toLowerCase())) {
                        let option = document.createElement("div");

                        // Display name, roll number, department, and course in the suggestion
                        option.innerHTML = `
                            <strong>${item.name}</strong><br>
                            Roll Number: ${item.roll_number}<br>
                            Department: ${item.department_name}<br>
                            Course: ${item.course_name}
                        `;
                        option.addEventListener("click", function() {
                            input.value = item.name;
                            document.getElementById("student_name_hidden").value = item.name;
                            dropdown.innerHTML = "";
                        });
                        dropdown.appendChild(option);
                    }
                });
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            let studentInput = document.getElementById("student_name");

            // Student details in JSON format
            let studentDetails = <?php echo json_encode($student_details); ?>;

            // Call the autocomplete function with the input element and student details
            autocomplete(studentInput, studentDetails);
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>Generate Reports</h2>
        
        <!-- Buttons to toggle visibility of report forms -->
        <div class="btn-container">
            <button class="toggle-btn" onclick="showReportForm('student-report')">Student Report</button>
            <button class="toggle-btn" onclick="showReportForm('club-report')">Club Report</button>
            <button class="toggle-btn" onclick="showReportForm('department-report')">Department Report</button>
            <button class="toggle-btn" onclick="showReportForm('event-report')">Event Report</button>
        </div>

        <!-- Student Report Form -->
        <div id="student-report" class="report-form">
            <div class="form-group">
                <label for="student_name">Student Report (Select Student Name)</label>
                <input type="text" id="student_name" name="student_name" placeholder="Start typing student name...">
                <div id="autocomplete-list"></div>
                <form action="student_report.php" method="post">
                    <input type="hidden" name="student_name_selected" id="student_name_hidden">
                    <label for="from_date_student">From Date</label>
                    <input type="date" id="from_date_student" name="from_date_student">
                    <label for="to_date_student">To Date</label>
                    <input type="date" id="to_date_student" name="to_date_student" style="margin-top: 10px;">
                    <button type="submit">Generate Report</button>
                </form>
            </div>
        </div>

        <!-- Club Report Form -->
        <div id="club-report" class="report-form">
            <div class="form-group">
                <form action="club_report.php" method="post">
                    <label for="from_date">Club Report<?php echo " ( $club_name )"; ?> (Select Date Range)</label>
                    <input type="date" id="from_date" name="from_date">
                    <input type="date" id="to_date" name="to_date" style="margin-top: 10px;">
                    <button type="submit">Generate Report</button>
                </form>
            </div>
        </div>

        <!-- Department Report Form -->
        <div id="department-report" class="report-form">
            <div class="form-group">
                <form action="department_report.php" method="post">
                    <label for="department_name">Department</label>
                    <select id="department_name" name="department_name" required>
                        <option value="">Select Department</option>
                        <?php
                        $query = "SELECT department_id, name FROM department";
                        $result = $conn->query($query);
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['department_id'] . "'>" . $row['name'] . "</option>";
                        }
                        ?>
                    </select>
                    <label for="from_date">From Date</label>
                    <input type="date" id="from_date" name="from_date" required>
                    <label for="to_date">To Date</label>
                    <input type="date" id="to_date" name="to_date" style="margin-top: 10px;" required>
                    <button type="submit">Generate Report</button>
                </form>
            </div>
        </div>

        <!-- Event Report Form -->
        <div id="event-report" class="report-form">
            <div class="form-group">
                <label for="event_name">Event Report (Select Event Name)</label>
                <form action="event_report.php" method="post">
                    <select id="event_name" name="event_id">
                    <?php
                    $stmt = $conn->prepare("SELECT e.event_id, e.name FROM event e JOIN request r ON e.event_id = r.event_id WHERE e.staff_id = ? AND r.approve > 0 GROUP BY e.event_id");
                    if ($stmt) {
                        $stmt->bind_param("i", $_SESSION['staff_id']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($row['event_id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                        }
                        $stmt->close();
                    }
                    ?>
                    </select>
                    <button type="submit">Generate Report</button>
                </form>
            </div>
        </div>

    </div>
</body>
</html>
