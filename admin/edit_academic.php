<?php
require 'db.php';

// 1️⃣ Handle filtering by faculty & year
if (isset($_GET['faculty'], $_GET['year_of_study'])) {
    $faculty     = $_GET['faculty'];
    $year        = $_GET['year_of_study'];

    $sql         = "SELECT * FROM students WHERE faculty = ? AND year_of_study = ?";
    $stmt        = $connection->prepare($sql);
    $stmt->bind_param("ii", $faculty, $year);
    $stmt->execute();
    $students    = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // fetch faculties for the filter dropdown
    $faculties    = $connection->query("SELECT * FROM faculty")->fetch_all(MYSQLI_ASSOC);
    $students     = null;
}

// 2️⃣ Handle actual deletion of academic records
if (isset($_GET['delete_id'])) {
    $sid = (int)$_GET['delete_id'];
    $del = $connection->prepare("DELETE FROM academics WHERE student_id = ?");
    $del->bind_param("i", $sid);
    $del->execute();
    $del->close();

    // redirect back to filtered view (if any) or to self
    $qs = isset($faculty,$year)
        ? "?faculty={$faculty}&year_of_study={$year}"
        : "";
    header("Location: delete_academics.php{$qs}");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Academic Records</title>
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

  <h2>Edit Academic Records</h2>

  <?php if ($students === null): ?>
    <!-- filter form -->
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
      <button type="submit" class="btn-submit">View Students</button>
    </form>
  <?php else: ?>
    <!-- student table -->
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Full Name</th><th>Date</th><th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($students)): ?>
          <tr><td colspan="4" style="text-align:center;">No students found.</td></tr>
        <?php else: ?>
          <?php foreach ($students as $s): ?>
          <tr>
            <td><?= htmlspecialchars($s['student_id']) ?></td>
            <td><?= htmlspecialchars($s['full_name']) ?></td>
            <td><?= htmlspecialchars($s['create_at']) ?></td> <!-- Correct field name here -->
            <td>
              <a href="edit_academics.php?student_id=<?= $s['student_id'] ?>" class="btn-edit">Edit</a>
            
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  <?php endif; ?>

</body>
</html>
