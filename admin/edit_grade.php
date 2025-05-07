<?php
require 'db.php';

// Fetch faculties
$faculties = $connection->query("SELECT * FROM faculty")->fetch_all(MYSQLI_ASSOC);

// Handle filtering
$grades = null;
if (isset($_GET['faculty'], $_GET['year_of_study'])) {
    $faculty = $_GET['faculty'];
    $year = $_GET['year_of_study'];

    $sql = "
        SELECT 
            students.student_id, students.full_name,
            grades.grade_id, grades.subject_name, grades.grade
        FROM students
        LEFT JOIN grades 
            ON students.student_id = grades.student_id
        WHERE students.faculty = ? AND students.year_of_study = ?
    ";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ii", $faculty, $year);
    $stmt->execute();
    $grades = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $gid = (int)$_GET['delete_id'];
    $del = $connection->prepare("DELETE FROM grades WHERE grade_id = ?");
    $del->bind_param("i", $gid);
    $del->execute();
    $del->close();

    $qs = isset($faculty, $year) ? "?faculty={$faculty}&year_of_study={$year}" : "";
    header("Location: delete_grades.php{$qs}");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Grades</title>
  <style>
    body {font-family:Arial; background:#f4f4f4; padding:20px;}
    form, table {background:#fff; padding:20px; margin:auto; border-radius:8px; box-shadow:0 0 10px #ccc; width:90%;}
    h2 {text-align:center;}
    select {padding:8px; width:48%; margin:1% 1% 10px 1%;}
    .btn-submit {background:green;color:#fff;padding:10px;border:none;border-radius:4px;cursor:pointer;width:100%;}
    table {width:100%;border-collapse:collapse;margin-top:20px;}
    th, td {border:1px solid #ccc; padding:10px;}
    th {background:#1E3A8A;color:#fff;}
    .btn-delete {background:red;color:#fff;padding:6px 12px;border:none;border-radius:4px;text-decoration:none;}
    .btn-edit, .btn-add {background:blue;color:#fff;padding:6px 12px;border:none;border-radius:4px;text-decoration:none;}
  </style>
</head>
<body>

<h2>Edit Grade Records</h2>

<?php if ($grades === null): ?>
  <!-- Filter form -->
  <form method="GET" action="">
    <select name="faculty" required>
      <option value="">-- Select Faculty --</option>
      <?php foreach ($faculties as $f): ?>
        <option value="<?= $f['faculties_id'] ?>"><?= htmlspecialchars($f['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="year_of_study" required>
      <option value="">-- Select Year --</option>
      <option value="1">Year 1</option>
      <option value="2">Year 2</option>
      <option value="3">Year 3</option>
      <option value="4">Year 4</option>
    </select>
    <button type="submit" class="btn-submit">View Grades</button>
  </form>
<?php else: ?>
  <!-- Display grades -->
  <table>
    <thead>
      <tr>
        <th>ID</th><th>Full Name</th><th>Subject</th><th>Grade</th><th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($grades)): ?>
        <tr><td colspan="5" style="text-align:center;">No students found.</td></tr>
      <?php else: ?>
        <?php foreach ($grades as $g): ?>
        <tr>
          <td><?= htmlspecialchars($g['student_id']) ?></td>
          <td><?= htmlspecialchars($g['full_name']) ?></td>
          <td><?= $g['subject_name'] ?? '<em>None</em>' ?></td>
          <td><?= $g['grade'] ?? '-' ?></td>
          <td>
            <?php if ($g['grade_id']): ?>
              <a href="edit_grades.php?grade_id=<?= $g['grade_id'] ?>" class="btn-edit">Edit</a>
              <a href="delete_grades.php?faculty=<?= $faculty ?>&year_of_study=<?= $year ?>&delete_id=<?= $g['grade_id'] ?>" class="btn-delete" onclick="return confirm('Delete this grade?');">Delete</a>
            <?php else: ?>
              <a href="add_grade.php?student_id=<?= $g['student_id'] ?>" class="btn-add">Edit</a>
              <a href="add_grade.php?student_id=<?= $g['student_id'] ?>&faculty=<?= $faculty ?>&year_of_study=<?= $year ?>" class="btn-add">Edit</a>

            <?php endif; ?>
          </td>
          
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
<?php endif; ?>

</body>
</html>
