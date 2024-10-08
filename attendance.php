<?php
// Include the database connection file
include("connection.php");

// Start the session
session_start();

if (!isset($_SESSION['event_id']) && isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    $_SESSION['event_id'] = $event_id; // Store the event_id in the session
}

// Handle the AJAX request to fetch student details
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rollNumber'])) {
    $rollNumber = $_POST['rollNumber'];
    
    error_log("Received roll number: " . $rollNumber); // Debugging line

    // Prepare and execute the query to fetch student details
    $sql = "SELECT s.name as student_name, c.name as course_name, d.name as department_name
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
        echo json_encode(array(
            'status' => 'registered',
            'student_name' => $row['student_name'],
            'course_name' => $row['course_name'],
            'department_name' => $row['department_name']
        ));
    } else {
        echo json_encode(array('status' => 'not_registered'));
    }

    // Close connections
    $stmt->close();
    $conn->close();
    exit(); // Ensure no further code is executed
}

// Handle the AJAX request to get event_id
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['event_name'])) {
    $event_name = $_GET['event_name'];

    if (!empty($event_name)) {
        $sql = "SELECT event_id FROM event WHERE name = ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare statement failed: " . $conn->error);
            echo json_encode(array('status' => 'error', 'message' => 'Database prepare failed.'));
            exit();
        }
        $stmt->bind_param("s", $event_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $event_id = (int) $row['event_id']; // Ensure $event_id is an integer
            $_SESSION['event_id'] = $event_id; // Store the event_id in the session
            error_log("Fetched event_id: " . $event_id); // Debugging line
            echo "Event ID: " . htmlspecialchars($event_id);
        } else {
            echo "No event found with the given name.";
        }

        $stmt->close();
    }
}

// Handle the request to insert roll numbers and event IDs into the request table
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_all'])) {
    // Get the event_id from the session
    if (!isset($_SESSION['event_id'])) {
        echo json_encode(array('status' => 'error', 'message' => 'Event ID not found in session.'));
        exit();
    }
    $event_id = $_SESSION['event_id'];  // Retrieve event_id from the session
    $roll_numbers = isset($_POST['roll_numbers']) ? json_decode($_POST['roll_numbers'], true) : array(); // Decode JSON array of roll numbers

    if (empty($roll_numbers)) {
        echo json_encode(array('status' => 'error', 'message' => 'No roll numbers provided.'));
        exit();
    }

    // Step 1: Insert the current date, event_id, and type into the 'day' table
    $current_date = date('Y-m-d'); // Get current date in 'YYYY-MM-DD' format
    $insertDaySql = "INSERT INTO day (date, event_id, type) VALUES (?, ?, 1)";
    $insertDayStmt = $conn->prepare($insertDaySql);
    if (!$insertDayStmt) {
        error_log("Prepare statement for day table failed: " . $conn->error);
        echo json_encode(array('status' => 'error', 'message' => 'Database prepare failed for day table.'));
        exit();
    }
    $insertDayStmt->bind_param("si", $current_date, $event_id); // Bind the date and event_id to the query
    if (!$insertDayStmt->execute()) {
        error_log("Execute failed for day table: " . $insertDayStmt->error);
        echo json_encode(array('status' => 'error', 'message' => 'Failed to insert day entry.'));
        exit();
    }
    
    // Step 2: Retrieve the last inserted 'date_id' from the 'day' table
    $date_id = $insertDayStmt->insert_id; // Get the auto-incremented ID from the last insert
    $insertDayStmt->close();

    // Prepare the query to check existing entries
    $checkSql = "SELECT COUNT(*) as count FROM request WHERE roll_number = ? AND event_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    if (!$checkStmt) {
        error_log("Prepare statement failed: " . $conn->error);
        echo json_encode(array('status' => 'error', 'message' => 'Database prepare failed.'));
        exit();
    }

    // Step 3: Prepare the query to insert into the request table (including date_id)
    $insertSql = "INSERT INTO request (roll_number, event_id, date_id) VALUES (?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    if (!$insertStmt) {
        error_log("Prepare statement failed: " . $conn->error);
        echo json_encode(array('status' => 'error', 'message' => 'Database prepare failed.'));
        exit();
    }

    $success = true;
    $messages = [];

    // Loop through the roll numbers and execute the check and insert queries
    foreach ($roll_numbers as $rollNumber) {
        // Check if the entry already exists
        $checkStmt->bind_param("si", $rollNumber, $event_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $checkRow = $checkResult->fetch_assoc();

        if ($checkRow['count'] > 0) {
            $messages[] = "Roll number $rollNumber has already submitted attendance.";
            $success = false;
        } else {
            // Step 4: Insert the new entry into the 'request' table, including the 'date_id'
            $insertStmt->bind_param("sii", $rollNumber, $event_id, $date_id);
            if (!$insertStmt->execute()) {
                error_log("Execute failed: " . $insertStmt->error);
                $messages[] = "Failed to insert roll number $rollNumber.";
                $success = false;
            }
        }
    }

    $checkStmt->close();
    $insertStmt->close();
    $conn->close();

    if ($success) {
        echo json_encode(array('status' => 'success', 'message' => 'Data inserted successfully.'));
    } else {
        echo json_encode(array('status' => 'warning', 'message' => implode(" ", $messages)));
    }
    exit(); // Ensure no further code is executed
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
            var eventId = document.querySelector("input[name='event_id']").value;
            
            // Collect all roll numbers from the table
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

            // Send the roll numbers and event ID to the server
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true); // Use the same PHP file
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.status === "success") {
                                alert("Data inserted successfully.");
                                window.location.href = "student_page.php"; // Redirect to studentpage.php
                            } else if (response.status === "warning") {
                                alert("Warning: " + response.message);
                            } else {
                                alert("Error: " + response.message);
                            }
                        } catch (e) {
                            console.error("Error parsing JSON response:", e);
                        }
                    } else { 
                        console.error("AJAX request failed with status:", xhr.status);
                    }
                }
            };
            xhr.send("submit_all=1&event_id=" + encodeURIComponent(eventId) + "&roll_numbers=" + encodeURIComponent(JSON.stringify(rollNumbers)));
        }
    </script>
</head>
<body>
    <h1>Student Information</h1>
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
    <input type="hidden" name="event_id" value="<?php echo isset($_GET['event_id']) ? htmlspecialchars($_GET['event_id']) : ''; ?>">
    <button class="action-button add-button" onclick="addRow()">Add</button>
    <button class="action-button submit-all-button" onclick="submitAll()">Submit All</button>
</body>
</html>
