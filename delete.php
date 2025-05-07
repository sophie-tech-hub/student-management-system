<?php
require 'config.php';
// Handle delete if ID is set via GET
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Delete records from related tables
    $sql = "DELETE FROM fees WHERE student_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();

    $sql = "DELETE FROM grades WHERE student_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();

    $sql = "DELETE FROM attendance WHERE student_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();

    // Assuming the correct table name is 'academics' or the actual table name
    $sql = "DELETE FROM academics WHERE student_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();

    // Finally delete the student from the students table
    $sql = "DELETE FROM students WHERE student_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $student_id);

    if ($stmt->execute()) {
        echo "<script>alert('Student and related records deleted successfully'); window.location.href = 'delete.php';</script>";
        exit;
    } else {
        echo "Error deleting student: " . $connection->error;
    }
}

// Fetch all students
$sql = "SELECT * FROM students";
$result = $connection->query($sql);
$students = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Student</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; background-color: #fff; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #1E3A8A; color: white; }
        .btn { padding: 6px 12px; text-decoration: none; border-radius: 4px; color: white; font-weight: bold; margin-right: 5px; }
        .btn-delete { background-color: red; }
        .btn-back { background-color: orange; margin-bottom: 15px; display: inline-block; }
        .actions { white-space: nowrap; }
    </style>
</head>
<body>

    <h2>Delete Student</h2>

    <a href="read.php" class="btn btn-back">← Back to Student Records</a>

    <table>
        <tr>
            <th>Student ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Faculty</th>
            <th>Year of Study</th>
            <th>Course Duration</th>
            <th>Delete</th>
        </tr>
        <?php foreach ($students as $student): ?>
        <tr>
            <td><?= htmlspecialchars($student['student_id']) ?></td>
            <td><?= htmlspecialchars($student['full_name']) ?></td>
            <td><?= htmlspecialchars($student['email']) ?></td>
            <td><?= htmlspecialchars($student['phone']) ?></td>
            <td><?= htmlspecialchars($student['faculty']) ?></td>
            <td><?= htmlspecialchars($student['year_of_study']) ?></td>
            <td><?= htmlspecialchars($student['course_duration']) ?></td>
            <td class="actions">
                <a href="delete.php?id=<?= $student['student_id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this student and all their records?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>
