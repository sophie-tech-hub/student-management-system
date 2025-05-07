<?php
require 'config.php';

// Check if ID is present in the URL
if (!isset($_GET['id'])) {
    echo "Student ID is missing.";
    exit;
}

$student_id = $_GET['id'];
$student = null;

// Fetch current student data
$sql = "SELECT * FROM students WHERE student_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $student = $result->fetch_assoc();
} else {
    echo "Student not found.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $faculty = $_POST['faculty'];
    $year_of_study = $_POST['year_of_study'];
    $course_duration = $_POST['course_duration'];

    $update_sql = "UPDATE students SET full_name = ?, email = ?, phone = ?, faculty = ?, year_of_study = ?, course_duration = ? WHERE student_id = ?";
    $update_stmt = $connection->prepare($update_sql);
    $update_stmt->bind_param("sssssis", $full_name, $email, $phone, $faculty, $year_of_study, $course_duration, $student_id);
    
    if ($update_stmt->execute()) {
        echo "<script>alert('Student updated successfully'); window.location.href = 'read.php';</script>";
    } else {
        echo "Error updating student: " . $connection->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }

        h2 {
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

        input[type="text"],
        input[type="email"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }

        .btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 15px;
            color: white;
            background-color: green;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Edit Student</h2>

<form method="POST">
    <label>Full Name</label>
    <input type="text" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>

    <label>Phone</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($student['phone']) ?>" required>

    <label>Faculty</label>
    <input type="text" name="faculty" value="<?= htmlspecialchars($student['faculty']) ?>" required>

    <label>Year of Study</label>
    <input type="number" name="year_of_study" value="<?= htmlspecialchars($student['year_of_study']) ?>" required>

    <label>Course Duration</label>
    <input type="text" name="course_duration" value="<?= htmlspecialchars($student['course_duration']) ?>" required>

    <button type="submit" class="btn">Update Student</button>
</form>

</body>
</html>
