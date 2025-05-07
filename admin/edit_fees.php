<?php
require 'db.php';
$editing_fee = null;

if (isset($_GET['fee_id'])) {
    $fee_id = (int) $_GET['fee_id'];
    $stmt = $connection->prepare("SELECT f.*, s.full_name FROM fees f JOIN students s ON f.student_id = s.student_id WHERE f.fee_id = ?");

    $stmt->bind_param("i", $fee_id);
    $stmt->execute();
    $editing_fee = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Handle update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fee_id'])) {
    $fee_id = $_POST['fee_id'];
    $amount_paid = $_POST['amount_paid'];
    $payment_date = $_POST['payment_date'];
    $fees_required = $_POST['fees_required'];
    $balance = $fees_required - $amount_paid;

    $update = $connection->prepare("UPDATE fees SET amount_paid = ?, payment_date = ?, fees_required = ?, balance = ? WHERE fee_id = ?");
    $update->bind_param("dsdii", $amount_paid, $payment_date, $fees_required, $balance, $fee_id);
    $update->execute();
    $update->close();

    echo "<script>alert('Fee record updated successfully');window.location.href='edit_fees.php';</script>";
    exit;
}


// Fetch faculties
$faculties = $connection->query("SELECT * FROM faculty")->fetch_all(MYSQLI_ASSOC);

// Handle filtering
$students = null;
if (isset($_GET['faculty'], $_GET['year_of_study'])) {
    $faculty = $_GET['faculty'];
    $year = $_GET['year_of_study'];

    $sql = "
        SELECT 
            students.student_id, students.full_name,
            fees.fee_id, fees.payment_date, fees.amount_paid, fees.balance
        FROM students
        LEFT JOIN fees 
            ON students.student_id = fees.student_id
        WHERE students.faculty = ? AND students.year_of_study = ?
    ";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ii", $faculty, $year);
    $stmt->execute();
    $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $fid = (int)$_GET['delete_id'];
    $del = $connection->prepare("DELETE FROM fees WHERE fee_id = ?");
    $del->bind_param("i", $fid);
    $del->execute();
    $del->close();

    $qs = isset($faculty, $year)
        ? "?faculty={$faculty}&year_of_study={$year}"
        : "";
    header("Location: delete_fees.php{$qs}");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Fee Records</title>
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

<h2>Edit Fee Records</h2>
<?php if ($editing_fee): ?>
    <form method="POST" action="">
        <input type="hidden" name="fee_id" value="<?= $editing_fee['fee_id'] ?>">
        <h3>Editing Fees for <?= htmlspecialchars($editing_fee['full_name']) ?></h3>

        <label>Amount Paid:</label>
        <input type="number" name="amount_paid" value="<?= $editing_fee['amount_paid'] ?>" required>

        <label>Payment Date:</label>
        <input type="date" name="payment_date" value="<?= $editing_fee['payment_date'] ?>" required>

        <label>Fees Required:</label>
        <input type="number" name="fees_required" value="<?= $editing_fee['fees_required'] ?>" required>

        <button type="submit" class="btn-submit">Update Fee</button>
    </form>
    <hr>
<?php endif; ?>


<?php if ($students === null): ?>
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
    <button type="submit" class="btn-submit">View Fees</button>
  </form>
<?php else: ?>
  <!-- Display students -->
  <table>
    <thead>
      <tr>
        <th>ID</th><th>Full Name</th><th>Payment Date</th><th>Amount Paid</th><th>Balance</th><th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($students)): ?>
        <tr><td colspan="6" style="text-align:center;">No students found.</td></tr>
      <?php else: ?>
        <?php foreach ($students as $s): ?>
        <tr>
          <td><?= htmlspecialchars($s['student_id']) ?></td>
          <td><?= htmlspecialchars($s['full_name']) ?></td>
          <td><?= $s['payment_date'] ?? '<em>No record</em>' ?></td>
          <td><?= $s['amount_paid'] ?? '-' ?></td>
          <td><?= $s['balance'] ?? '-' ?></td>
          <td>
            <?php if ($s['fee_id']): ?>
              <a href="edit_fees.php?fee_id=<?= $s['fee_id'] ?>" class="btn-edit">Edit</a>
              <a href="delete_fees.php?faculty=<?= $faculty ?>&year_of_study=<?= $year ?>&delete_id=<?= $s['fee_id'] ?>" class="btn-delete" onclick="return confirm('Delete this fee record?');">Delete</a>
            <?php else: ?>
             
              <a href="edit_fees.php?fee_id=<?= $s['fee_id'] ?>" class="btn-edit" title="Edit fee record">Edit</a>

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
