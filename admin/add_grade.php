<?php
require 'db.php';

// Handle form filtering
if (isset($_GET['student_id'])) {
    // Load a single student by ID
    $student_id = $_GET['student_id'];
    $stmt = $connection->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $students_result = $stmt->get_result();
} elseif (isset($_GET['faculty']) && isset($_GET['year_of_study'])) {
    $faculty = $_GET['faculty'];
    $year = $_GET['year_of_study'];
    $stmt = $connection->prepare("SELECT * FROM students WHERE faculty = ? AND year_of_study = ?");
    $stmt->bind_param("ii", $faculty, $year);
    $stmt->execute();
    $students_result = $stmt->get_result();
} else {
    $faculties_result = $connection->query("SELECT * FROM faculty");
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Grades</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        h2 { text-align: center; }
        .btn, .btn-submit { padding: 8px 12px; border-radius:4px; color: #fff; text-decoration:none; font-weight:bold; }
        .btn-submit       { background: green; display:block; width:200px; margin:10px auto; text-align:center; }
        .btn-view         { background: orange; display:inline-block; margin-bottom:15px; }
        table { width:100%; border-collapse:collapse; background:#fff; margin:auto; }
        th, td { border:1px solid #ccc; padding:10px; text-align:left; }
        th      { background:#1E3A8A; color:#fff; }
        form    { margin-bottom:20px; }
        select  { padding:6px; width:200px; margin-right:10px; }
        input[type="text"] { width:100%; padding:6px; }
    </style>
</head>
<body>

<?php if (!isset($students_result)) { ?>
    <h2>Select Faculty & Year</h2>
    <form method="GET" action="">
        <select name="faculty" required>
            <option value="">-- Faculty --</option>
            <?php while ($f = $faculties_result->fetch_assoc()): ?>
            <option value="<?= $f['faculties_id'] ?>"><?= htmlspecialchars($f['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <select name="year_of_study" required>
            <option value="">-- Year --</option>
            <option value="1">Year 1</option>
            <option value="2">Year 2</option>
            <option value="3">Year 3</option>
            <option value="4">Year 4</option>
        </select>

        <button class="btn-submit" type="submit">View Students</button>
    </form>
<?php } else { ?>
    <h2>Enter Grades</h2>
    <form method="POST" action="save_grade.php">
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Subject</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($s = $students_result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <!-- CORRECTION: Hidden field to pass student_id[] -->
                        <input type="hidden" name="student_id[]" value="<?= $s['student_id'] ?>">
                        <?= htmlspecialchars($s['student_id']) ?>
                    </td>
                    <td><?= htmlspecialchars($s['full_name']) ?></td>
                    <td>
                        <!-- CORRECTION: name="subject_name[]" to match your save script -->
                        <input type="text" name="subject_name[]" required>
                    </td>
                    <td>
                        <!-- CORRECTION: name="grade[]" -->
                        <input type="text" name="grade[]" required>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- keep your btn-save class if you like, but styled to match -->
        <button type="submit" class="btn-submit">Save Grades</button>
    </form>
<?php } ?>

</body>
</html>
