<?php
include("connection.php"); // Include the database connection file

session_start(); // Start the session

// Check if the club_id session variable is set
if (isset($_SESSION['club_id'])) {
    $club_id = $_SESSION['club_id'];
} else {
    // Handle the case where the club_id session variable is not set
    echo "Club ID is not set.";
    exit();
}

// Handle the AJAX request to fetch student details
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rollNumber'])) {
    $rollNumber = $_POST['rollNumber'];
    error_log("Received roll number: " . $rollNumber); // Debugging line

    // Prepare and execute the query to fetch student details
    $sql = "SELECT s.name as student_name, c.name as course_name, d.name as department_name, s.club_id
            FROM student s
            JOIN course c ON s.course_id = c.course_id
            JOIN department d ON c.department_id = d.department_id
            WHERE s.roll_number = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare statement failed: " . $conn->error);
        echo json_encode(array('status' => 'error', 'message' => 'Database prepare failed.'));
        exit();
    }

    $stmt->bind_param("s", $rollNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any record was found
    if ($row = $result->fetch_assoc()) {
        if ($row['club_id'] > 0) {
            // Check if the student is already added to a club
            echo json_encode(array('status' => 'error', 'message' => 'Student already added to a club.'));
        } else {
            echo json_encode(array(
                'status' => 'registered',
                'student_name' => $row['student_name'],
                'course_name' => $row['course_name'],
                'department_name' => $row['department_name']
            ));
        }
    } else {
        echo json_encode(array('status' => 'not_registered'));
    }

    $stmt->close();
    exit(); // Ensure no further code is executed
}

// Handle the "Submit All" button press
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitAll'])) {
    $rollNumbers = json_decode($_POST['rollNumbers']);
    $club_id = $_SESSION['club_id'];

    $existingRollNumbers = [];

    // Check if club_id is already added for the roll numbers
    $checkSql = "SELECT roll_number FROM student WHERE club_id = ? AND roll_number IN (" . implode(',', array_fill(0, count($rollNumbers), '?')) . ")";
    $checkStmt = $conn->prepare($checkSql);
    $params = array_merge([$club_id], $rollNumbers);
    $types = str_repeat('s', count($rollNumbers) + 1);
    $checkStmt->bind_param($types, ...$params);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    while ($row = $checkResult->fetch_assoc()) {
        $existingRollNumbers[] = $row['roll_number'];
    }

    // Close the check statement
    $checkStmt->close();

    if (!empty($existingRollNumbers)) {
        echo json_encode(array('status' => 'error', 'message' => 'Club ID already added for roll numbers: ' . implode(', ', $existingRollNumbers)));
        exit();
    }

    // Prepare the query to insert club_id for each roll number
    $sql = "INSERT INTO student (roll_number, club_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE club_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("Prepare statement failed: " . $conn->error);
        echo json_encode(array('status' => 'error', 'message' => 'Database prepare failed.'));
        exit();
    }

    // Bind the parameters and execute for each roll number
    foreach ($rollNumbers as $rollNumber) {
        $stmt->bind_param("sss", $rollNumber, $club_id, $club_id);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();

    echo json_encode(array('status' => 'success', 'message' => 'Club ID inserted successfully.'));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Table</title> 
    <link rel="stylesheet" type="text/css" href="student_style.css">
    <script>
        let rowIndex = 1;

        function validateRollNumber(form, index) {
            var rollNumber = form.rollNumber.value;
            var eventId = document.querySelector("input[name='event_id']").value;
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true); // Use the same PHP file
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            var row = document.querySelector(`#row-${index}`);
                            if (response.status === "not_registered") {
                                alert("Roll number not registered.");
                                row.querySelector(".student-name").textContent = "";
                                row.querySelector(".course-name").textContent = "";
                                row.querySelector(".department-name").textContent = "";
                            } else if (response.status === "registered") {
                                row.querySelector(".student-name").textContent = response.student_name;
                                row.querySelector(".course-name").textContent = response.course_name;
                                row.querySelector(".department-name").textContent = response.department_name;
                            } else if (response.status === "error") {
                                alert(response.message); // Show error message if student is already added to a club
                            }
                        } catch (e) {
                            console.error("Error parsing JSON response:", e);
                        }
                    } else {
                        console.error("AJAX request failed with status:", xhr.status);
                    }
                }
            };
            xhr.send("rollNumber=" + encodeURIComponent(rollNumber) + "&event_id=" + encodeURIComponent(eventId));
            return false; // Prevent form submission
        }

        function addRow() {
            rowIndex++;
            var tableBody = document.querySelector("tbody");
            var newRow = document.createElement("tr");
            newRow.id = `row-${rowIndex}`;
            newRow.innerHTML = `
                <td>
                    <form onsubmit="return validateRollNumber(this, ${rowIndex});">
                        <input type="text" name="rollNumber" class="input-field" placeholder="Enter Roll Number" oninput="updateYearHeading()">
                        <input type="submit" class="action-button check-button" value="Check">
                    </form>
                </td>
                <td class="student-name"></td>
                <td class="course-name"></td>
                <td class="department-name"></td>
                <td class="year-heading">---</td>
                <td><button class="action-button remove-button" onclick="removeRow(${rowIndex})">Remove</button></td>
            `;
            tableBody.appendChild(newRow);
        }

        function removeRow(index) {
            var row = document.querySelector(`#row-${index}`);
            row.remove();
        }

        function getYearHeading(rollNumber) {
            // Extract the first two digits of the roll number
            var prefix = rollNumber.substring(0, 2);
            var year = '20' + prefix;
            return year;
        }

        function updateYearHeading() {
            var rows = document.querySelectorAll("tbody tr");
            rows.forEach(row => {
                var rollNumberInput = row.querySelector("input[name='rollNumber']");
                if (rollNumberInput) {
                    var yearHeading = getYearHeading(rollNumberInput.value);
                    row.querySelector(".year-heading").textContent = yearHeading;
                }
            });
        }

        function submitAll() {
            var rollNumbers = [];
            var rows = document.querySelectorAll("tbody tr");
            rows.forEach(row => {
                var rollNumberInput = row.querySelector("input[name='rollNumber']");
                if (rollNumberInput) {
                    var rollNumber = rollNumberInput.value;
                    if (rollNumber) {
                        rollNumbers.push(rollNumber);
                    }
                }
            });

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true); // Use the same PHP file
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            alert(response.message);
                            window.location.href = "staff_page.php"; // Redirect to staff_page.php
                        } catch (e) {
                            console.error("Error parsing JSON response:", e);
                        }
                    } else {
                        console.error("AJAX request failed with status:", xhr.status);
                    }
                }
            };

            xhr.send("rollNumbers=" + JSON.stringify(rollNumbers) + "&submitAll=true");
        }
    </script>
</head>
<body>
    <h2> ADD STUDENT TO CLUB</h2>
    <table>
        <thead>
            <tr>
                <th>Roll Number</th>
                <th>Name</th>
                <th>Course</th>
                <th>Department</th>
                <th>Year</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr id="row-1">
                <td>
                    <form onsubmit="return validateRollNumber(this, 1);">
                        <input type="text" name="rollNumber" class="input-field" placeholder="Enter Roll Number" oninput="updateYearHeading()">
                        <input type="submit" class="action-button check-button" value="Check">
                    </form>
                </td>
                <td class="student-name"></td>
                <td class="course-name"></td>
                <td class="department-name"></td>
                <td class="year-heading">---</td>
                <td><button class="action-button remove-button" onclick="removeRow(1)">Remove</button></td>
            </tr>
            <!-- Additional rows can be added here -->
        </tbody>
    </table>
    <input type="hidden" name="event_id" value="<?php echo isset($_GET['event_id']) ? htmlspecialchars($_GET['event_id']) : ''; ?>"> <br>
    <button class="action-button add-button" onclick="addRow()">Add</button>
    <button class="action-button submit-all-button" onclick="submitAll()">Submit All</button>
</body>
</html>
