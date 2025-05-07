<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

require 'config.php';

// Fetch student data based on session student_id
$student_id = $_SESSION['student_id'];

// Fetch fee data
$fee_sql = "SELECT * FROM fees WHERE student_id = ?";
$fee_stmt = $connection->prepare($fee_sql);
$fee_stmt->bind_param("s", $student_id);
$fee_stmt->execute();
$fee_result = $fee_stmt->get_result();
$fees = $fee_result->fetch_assoc();

// Fetch attendance data
$attendance_sql = "SELECT * FROM attendance WHERE student_id = ?";
$attendance_stmt = $connection->prepare($attendance_sql);
$attendance_stmt->bind_param("s", $student_id);
$attendance_stmt->execute();
$attendance_result = $attendance_stmt->get_result();
$attendance = $attendance_result->fetch_assoc();

// Fetch academic data (GPA and subjects)
$academic_sql = "SELECT * FROM academics WHERE student_id = ?";
$academic_stmt = $connection->prepare($academic_sql);
$academic_stmt->bind_param("s", $student_id);
$academic_stmt->execute();
$academic_result = $academic_stmt->get_result();
$academic = $academic_result->fetch_assoc();

// Fetch grades
$grades_sql = "SELECT * FROM grades WHERE student_id = ?";
$grades_stmt = $connection->prepare($grades_sql);
$grades_stmt->bind_param("s", $student_id);
$grades_stmt->execute();
$grades_result = $grades_stmt->get_result();
$grades = $grades_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1E1E1E;
            color: white;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            margin: auto;
        }
        .header {
            background-color: #2196F3;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }
        .welcome {
            text-align: center;
            margin-top: 10px;
            font-size: 18px;
            color: #a3ffb2;
        }
        .nav {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background-color: #2E2E2E;
        }
        .nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: 0.3s;
            background-color: #2196F3;
            font-weight: bold;
        }
        .nav a:hover {
            background-color: #1769aa;
        }
        .dashboard {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background-color: #2E2E2E;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }
        .view-details {
            display: block;
            text-align: center;
            margin-top: 10px;
            background-color: #2196F3;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .view-details:hover {
            background-color: #1769aa;
        }
    </style>
</head>
<body>

    <div class="header">Student Portal</div>
    <div class="welcome">Welcome, <?= htmlspecialchars($_SESSION['student_name']) ?>!</div>

    <div class="nav">
        <a href="notices.html" target="_blank">📢 Announcements</a>
        <a href="contact.html" target="_blank">📞 Support Center</a>
        <a href="logout.php" style="background-color: red;">🚪 Secure Logout</a>
    </div>

    <div class="container">
        <div class="dashboard">
            <div class="card">
                <h2>Fees</h2>
                <p>Current Balance: $<?= isset($fees['balance']) ? htmlspecialchars($fees['balance']) : 'N/A' ?></p>
                <p>Recent Payments: $<?= isset($fees['recent_payments']) ? htmlspecialchars($fees['recent_payments']) : 'N/A' ?></p>
                <a href="student_fees.php?id=<?= $_SESSION['student_id'] ?>" class="view-details">View Details</a>
            </div>
            <div class="card">
                <h2>Attendance</h2>
                <p>Present: <?= isset($attendance['percentage']) ? htmlspecialchars($attendance['percentage']) : 'N/A' ?>%</p>
                <p>Missed Classes: <?= isset($attendance['missed_classes']) ? htmlspecialchars($attendance['missed_classes']) : 'N/A' ?></p>
                <a href="student_attendance.php?id=<?= $_SESSION['student_id'] ?>" class="view-details">View Details</a>
            </div>
            <div class="card">
                <h2>Academics</h2>
                <p>GPA: <?= isset($academic['gpa']) ? htmlspecialchars($academic['gpa']) : 'N/A' ?></p>
                <p>Subjects Taken: <?= isset($academic['subjects_taken']) ? htmlspecialchars($academic['subjects_taken']) : 'N/A' ?></p>
                <a href="student_academics.php?id=<?= $_SESSION['student_id'] ?>" class="view-details">View Details</a>
            </div>
            <div class="card">
                <h2>Grades</h2>
                <p>Math: <?= isset($grades['math']) ? htmlspecialchars($grades['math']) : 'N/A' ?></p>
                <p>Science: <?= isset($grades['science']) ? htmlspecialchars($grades['science']) : 'N/A' ?></p>
                <a href="student_grades.php?id=<?= $_SESSION['student_id'] ?>" class="view-details">View Details</a>
            </div>
        </div>
    </div>

</body>
</html>
