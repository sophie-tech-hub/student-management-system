<?php
require 'config.php';

if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Fetch student attendance records
    $sql = "SELECT * FROM attendance WHERE student_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $attendance = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $attendance[] = $row;
        }
    } else {
        echo "No attendance records found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Attendance</title>
</head>
<body>
    <h2>Student Attendance Records</h2>
    <table>
        <tr>
            <th>Date</th>
            <th>Status</th>
        </tr>
        <?php foreach ($attendance as $attend): ?>
        <tr>
            <td><?= htmlspecialchars($attend['date']) ?></td>
            <td><?= htmlspecialchars($attend['status']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
