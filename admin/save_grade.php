<?php
require 'db.php'; // Adjust the path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_ids = $_POST['student_id'] ?? [];
    $subjects = $_POST['subject_name'] ?? [];
    $grades = $_POST['grade'] ?? [];

    for ($i = 0; $i < count($student_ids); $i++) {
        $student_id = $student_ids[$i];
        $subject = $subjects[$i];
        $grade = $grades[$i];

        if (!empty($student_id) && !empty($subject) && !empty($grade)) {
            $stmt = $connection->prepare("INSERT INTO grades (student_id, subject_name, grade) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $student_id, $subject, $grade);
            $stmt->execute();
            $stmt->close();
        }
    }

    echo "<p style='color: green;'>Grades saved successfully.</p>";
} else {
    echo "Invalid request.";
}
?>
