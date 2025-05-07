<?php
include 'db.php';

$message = '';

// Fetch all faculties for the dropdown
$faculties_query = "SELECT * FROM faculty"; 
$faculties_result = $connection->query($faculties_query);

// Handle form submission for multiple records
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if 'faculty' and 'year_of_study' are selected
    if (isset($_POST['faculty']) && isset($_POST['year_of_study'])) {
        $faculty_id = $_POST['faculty'];
        $year_of_study = $_POST['year_of_study'];

        // Fetch students based on selected faculty and year of study
        $students_query = "SELECT * FROM students WHERE faculties_id = ? AND year_of_study = ?";
        $stmt = $connection->prepare($students_query);
        $stmt->bind_param("ss", $faculty_id, $year_of_study);
        $stmt->execute();
        $students_result = $stmt->get_result();

        // If no students found, set a message
        if ($students_result->num_rows === 0) {
            $message = "No students found for the selected faculty and year of study.";
        }

        // Handle student data submission
        if (isset($_POST['students']) && !empty($_POST['students'])) {
            $students_data = $_POST['students']; // Get all student data from the form

            foreach ($students_data as $student) {
                $student_id = $student['student_id'];
                $subject_taken = $student['subject_taken'];
                $grade = $student['grade'];

                if (!empty($student_id) && !empty($subject_taken) && !empty($grade)) {
                    $sql = "INSERT INTO academics (student_id, subject_taken, grade, faculties_id) VALUES (?, ?, ?, ?)";
                    $stmt = $connection->prepare($sql);
                    $stmt->bind_param("issi", $student_id, $subject_taken, $grade, $faculty_id);

                    if (!$stmt->execute()) {
                        $message = "Error: " . $stmt->error;
                        break;
                    }
                    $stmt->close();
                } else {
                    $message = "Please fill out all fields.";
                    break;
                }
            }

            if (empty($message)) {
                $message = "Academic records added successfully!";
            }
        } else {
            $message = "No student data submitted.";
        }
    } else {
        $message = "Please select both Faculty and Year of Study.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Academic Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
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
            color: white;
            font-weight: bold;
        }

        .btn-add {
            background-color: orange;
            margin-bottom: 10px;
            display: inline-block;
        }
    </style>
</head>
<body>

    <h2>Add Academic Records</h2>

    <?php if ($message) echo "<p><strong>$message</strong></p>"; ?>

    <!-- Faculty and Year Selection Form -->
    <form action="add_academic.php" method="POST">
        <label for="faculty">Select Faculty:</label>
        <select name="faculty" id="faculty" required>
            <option value="">Select Faculty</option>
            <?php while ($faculty = $faculties_result->fetch_assoc()) { ?>
                <option value="<?= $faculty['faculties_id'] ?>" <?= isset($_POST['faculty']) && $_POST['faculty'] == $faculty['faculties_id'] ? 'selected' : '' ?>>
                    <?= $faculty['name'] ?>
                </option>
            <?php } ?>
        </select>

        <label for="year_of_study">Select Year of Study:</label>
        <select name="year_of_study" id="year_of_study" required>
            <option value="">Select Year of Study</option>
            <option value="year 1" <?= isset($_POST['year_of_study']) && $_POST['year_of_study'] == 'year 1' ? 'selected' : '' ?>>Year 1</option>
            <option value="year 2" <?= isset($_POST['year_of_study']) && $_POST['year_of_study'] == 'year 2' ? 'selected' : '' ?>>Year 2</option>
            <option value="year 3" <?= isset($_POST['year_of_study']) && $_POST['year_of_study'] == 'year 3' ? 'selected' : '' ?>>Year 3</option>
            <option value="year 4" <?= isset($_POST['year_of_study']) && $_POST['year_of_study'] == 'year 4' ? 'selected' : '' ?>>Year 4</option>
        </select>

        <br><br>

        <button type="submit" class="btn-add">Show Students</button>
    </form>

    <?php if (isset($students_result) && $students_result->num_rows > 0) { ?>
        <!-- Table for Adding Academic Records -->
        <form action="add_academic.php" method="POST">
            <table>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Subject Taken</th>
                    <th>Grade</th>
                </tr>

                <?php while ($student = $students_result->fetch_assoc()) { ?>
                    <tr>
                        <td>
                            <input type="hidden" name="students[<?php echo $student['student_id']; ?>][student_id]" value="<?php echo $student['student_id']; ?>">
                            <?php echo $student['student_id']; ?>
                        </td>
                        <td>
                            <?php echo $student['full_name']; ?>
                        </td>
                        <td>
                            <input type="text" name="students[<?php echo $student['student_id']; ?>][subject_taken]" placeholder="Subject Taken" required>
                        </td>
                        <td>
                            <input type="text" name="students[<?php echo $student['student_id']; ?>][grade]" placeholder="Grade" required>
                        </td>
                    </tr>
                <?php } ?>
            </table>

            <br>

            <button type="submit" class="btn-add">Add Academic Records</button>
        </form>
    <?php } ?>

</body>
</html>
