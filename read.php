<?php
require 'config.php';

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
    <title>Student Records</title>
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

        .btn-edit {
            background-color: green;
        }

        .btn-delete {
            background-color: red;
        }

        .btn-add {
            background-color: orange;
            margin-bottom: 10px;
            display: inline-block;
        }
    </style>
</head>
<body>

    <h2>Student Records</h2>

   

    <table>
        <tr>
            <th>Student ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Faculty</th>
            <th>Year of Study</th>
            <th>Course Duration</th>
           
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
           
        </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>
