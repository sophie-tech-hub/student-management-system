<?php
require 'db.php';  // Include database connection

// Handle form submission for selecting Faculty and Year of Study
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['faculty']) && isset($_GET['year_of_study'])) {
    $faculty = $_GET['faculty'];
    $year_of_study = $_GET['year_of_study'];

    // Fetch students based on the selected faculty and year of study
    $sql = "SELECT * FROM students WHERE faculty = ? AND year_of_study = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ii", $faculty, $year_of_study);
    $stmt->execute();
    $students_result = $stmt->get_result();
    
    // Store the current date in a variable
    $current_date = date('Y-m-d');

    // Fetch the attendance data for the selected students
    // We only need to match on student_id and date
    $attendance_sql = "SELECT * FROM attendance WHERE date = ?";
    $attendance_stmt = $connection->prepare($attendance_sql);
    $attendance_stmt->bind_param("s", $current_date); // Now passing the variable
    $attendance_stmt->execute();
    $attendance_result = $attendance_stmt->get_result();
} else {
    // Fetch all faculties for dropdown (only for the first page)
    $faculties_query = "SELECT * FROM faculty";
    $faculties_result = $connection->query($faculties_query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> Attendance List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        h1, h2 {
            text-align: center;
        }

        form {
            background: #fff;
            padding: 20px;
            width: 400px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }

        .btn-submit {
            background-color: green;
            color: white;
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #1E3A8A;
            color: white;
        }

        .btn {
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            color: black;
            font-weight: bold;
            background-color: white;
            border: 1px solid #ccc;
        }

        .btn-present {
            background-color: yellow;
            color: black;
        }

        .btn-absent {
            background-color: red;
            color: white;
        }
    </style>
</head>
<body>

<?php if (!isset($students_result)) { ?>
    <!-- First Page: Faculty and Year Selection Form -->
    <h1>Select Faculty and Year of Study</h1>

    <form action="" method="GET">
        <!-- Faculty Dropdown -->
        <label for="faculty">Select Faculty: </label>
        <select name="faculty" id="faculty" required>
            <option value="">Select Faculty</option>
            <?php while ($faculty = $faculties_result->fetch_assoc()) { ?>
                <option value="<?php echo $faculty['faculties_id']; ?>"><?php echo $faculty['name']; ?></option>
            <?php } ?>
        </select>
        <br><br>

        <!-- Year of Study Dropdown -->
        <label for="year_of_study">Select Year of Study: </label>
        <select name="year_of_study" id="year_of_study" required>
            <option value="">Select Year</option>
            <option value="1">Year 1</option>
            <option value="2">Year 2</option>
            <option value="3">Year 3</option>
            <option value="4">Year 4</option>
        </select>
        <br><br>

        <!-- Submit Button -->
        <button type="submit" class="btn-submit">View Students</button>
    </form>
<?php } else { ?>
    <!-- Second Page: Display Student Records and Attendance -->
    <h2>Student Attendance Records</h2>

    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Full Name</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($student = $students_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                    <td>
                        <!-- You can set the date based on today's date or any logic you want -->
                        <input type="date" value="<?php echo date('Y-m-d'); ?>" disabled>
                    </td>
                    <td>
                        <?php
                        // Check if there's an attendance record for this student
                        $attendance_status = 'Not Recorded'; // Default if no record is found
                        while ($attendance = $attendance_result->fetch_assoc()) {
                            if ($attendance['student_id'] == $student['student_id'] && $attendance['date'] == $current_date) {
                                $attendance_status = $attendance['status']; // Either 'present' or 'absent'
                                break;
                            }
                        }
                        ?>
                        <span class="<?php echo ($attendance_status == 'present') ? 'btn-present' : 'btn-absent'; ?>">
                            <?php echo ucfirst($attendance_status); ?>
                        </span>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>

</body>
</html>
