<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
<h1>Welcome, <?= $_SESSION['admin_name'] ?></h1>

<ul>
    <li><a href="read_students.php">📋 View All Students</a></li>
    <li><a href="fees_crud.php">💰 Manage Fees</a></li>
    <li><a href="grades_crud.php">📊 Manage Grades</a></li>
    <li><a href="attendance_crud.php">🗓 Manage Attendance</a></li>
    <li><a href="academics_crud.php">📚 Manage Academics</a></li>
    <li><a href="../logout.php">🚪 Logout</a></li>
</ul>
</body>
</html>