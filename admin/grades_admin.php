<?php
include '../includes/db.php'; // Include the database connection

// Fetch faculties for the filter
$faculty_query = "SELECT * FROM faculty";
$faculty_result = $connection->query($faculty_query);

// Fetch grades filtered by faculty and year
$faculty_id = isset($_GET['faculty_id']) ? $_GET['faculty_id'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$sql = "SELECT grades.id, student.name, grades.grade, grades.subject, grades.created_at 
        FROM grades 
        JOIN student ON grades.student_id = student.id
        WHERE student.faculty_id = '$faculty_id' AND student.year = '$year'";
$grades_result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grades Management</title>
</head>
<body>
    <h1>Manage Grades</h1>

    <!-- Filter Form -->
    <form action="grades_admin.php" method="GET">
        <select name="faculty_id">
            <option value="">Select Faculty</option>
            <?php while ($faculty = $faculty_result->fetch_assoc()) { ?>
                <option value="<?php echo $faculty['id']; ?>" <?php echo ($faculty['id'] == $faculty_id) ? 'selected' : ''; ?>>
                    <?php echo $faculty['name']; ?>
                </option>
            <?php } ?>
        </select>
        
        <select name="year">
            <option value="">Select Year</option>
            <option value="1" <?php echo ($year == 1) ? 'selected' : ''; ?>>Year 1</option>
            <option value="2" <?php echo ($year == 2) ? 'selected' : ''; ?>>Year 2</option>
            <option value="3" <?php echo ($year == 3) ? 'selected' : ''; ?>>Year 3</option>
            <option value="4" <?php echo ($year == 4) ? 'selected' : ''; ?>>Year 4</option>
        </select>
        
        <button type="submit">Filter</button>
    </form>

    <!-- Grades Table -->
    <table border="1">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Subject</th>
                <th>Grade</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($grade = $grades_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $grade['name']; ?></td>
                    <td><?php echo $grade['subject']; ?></td>
                    <td><?php echo $grade['grade']; ?></td>
                    <td><?php echo $grade['created_at']; ?></td>
                    <td>
                        <a href="edit_grade.php?id=<?php echo $grade['id']; ?>">Edit</a> |
                        <a href="delete_grade.php?id=<?php echo $grade['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <br>
    <a href="add_grade.php">Add New Grade</a>
</body>
</html>
