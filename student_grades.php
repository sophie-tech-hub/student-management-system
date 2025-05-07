<?php
require 'config.php';

if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Fetch student grades
    $sql = "SELECT * FROM grades WHERE student_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $grades = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $grades[] = $row;
        }
    } else {
        echo "No grade records found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Grades</title>
</head>
<body>
    <h2>Student Grade Records</h2>
    <table>
        <tr>
            <th>Course Name</th>
            <th>Grade</th>
            <th>Academic Year</th>
        </tr>
        <?php foreach ($grades as $grade): ?>
        <tr>
            <td><?= htmlspecialchars($grade['course_name']) ?></td>
            <td><?= htmlspecialchars($grade['grade']) ?></td>
            <td><?= htmlspecialchars($grade['academic_year']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
