<?php
session_start();
require 'config.php';

$message = '';
$showLogin = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ['student_id','full_name','email','password','faculty','year_of_study','course_duration'];
    $errors = [];

    foreach ($required as $field) {
        if (empty(trim($_POST[$field] ?? ''))) {
            $errors[] = ucfirst(str_replace('_',' ',$field)) . ' is required.';
        }
    }

    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }

    if (empty($errors)) {
        $student_id      = htmlspecialchars(trim($_POST['student_id']));
        $full_name       = htmlspecialchars(trim($_POST['full_name']));
        $email           = htmlspecialchars(trim($_POST['email']));
        $phone           = htmlspecialchars(trim($_POST['phone'] ?? ''));
        $faculty         = htmlspecialchars(trim($_POST['faculty']));
        $year_of_study   = (int) $_POST['year_of_study'];
        $course_duration = (int) $_POST['course_duration'];
        $password_hash   = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Check if student_id already exists
        $check = $connection->prepare("SELECT student_id FROM students WHERE student_id = ?");
        $check->bind_param("s", $student_id);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $errors[] = 'Student ID already exists. Please choose a different one.';
        }
        $check->close();

        if (empty($errors)) {
            $sql = "INSERT INTO students 
                    (student_id, full_name, email, phone, faculty, year_of_study, course_duration, password)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            if ($stmt = $connection->prepare($sql)) {
                $stmt->bind_param(
                    'sssssiis',
                    $student_id,
                    $full_name,
                    $email,
                    $phone,
                    $faculty,
                    $year_of_study,
                    $course_duration,
                    $password_hash
                );
                if ($stmt->execute()) {
                    $message = "<p class='success'>Registered successfully!<br>Your Student ID is: <strong>$student_id</strong><br>Use it to login.</p>";
                    $showLogin = true;
                } else {
                    $message = '<p class="error">Database error: ' . htmlspecialchars($stmt->error) . '</p>';
                }
                $stmt->close();
            } else {
                $message = '<p class="error">Prepare failed: ' . htmlspecialchars($connection->error) . '</p>';
            }
        } else {
            $message = '<ul class="error"><li>' . implode('</li><li>', $errors) . '</li></ul>';
        }
    } else {
        $message = '<ul class="error"><li>' . implode('</li><li>', $errors) . '</li></ul>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Portal</title>
  <style>
    body {
      background-color: black;
      color: white;
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      flex-direction: column;
    }
    .container {
      width: 350px;
      background-color: #333;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
      text-align: center;
      border: 2px solid grey;
      margin-bottom: 20px;
    }
    .header {
      font-size: 20px;
      font-weight: bold;
      margin-bottom: 20px;
    }
    .input-group {
      margin: 10px 0;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }
    label {
      color: white;
      margin-bottom: 5px;
    }
    input, select {
      width: 100%;
      padding: 8px;
      border: none;
      border-radius: 5px;
      background-color: #555;
      color: white;
    }
    .button-group {
      margin-top: 20px;
    }
    button {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 5px;
      background-color: #1E3A8A;
      color: white;
      cursor: pointer;
    }
    .error { color: #ff4c4c; }
    .success { color: #4caf50; }
  </style>
</head>
<body>

  <?= $message ?>

  <!-- Registration Form -->
  <div class="container" id="register-container" style="<?= $showLogin ? 'display:none;' : '' ?>">
    <div class="header">STUDENT REGISTRATION</div>
    <form method="POST" action="">
      <div class="input-group">
        <label for="student_id">Student ID</label>
        <input type="text" name="student_id" value="<?= htmlspecialchars($_POST['student_id'] ?? '') ?>" required>
      </div>
      <div class="input-group">
        <label for="name">Full Name</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
      </div>
      <div class="input-group">
        <label for="email">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      </div>
      <div class="input-group">
        <label for="phone">Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
      </div>
      <div class="input-group">
        <label for="faculty">Faculty</label>
        <select name="faculty" required>
          <option value="">— select faculty —</option>
          <?php
            $faculties = [
              'Faculty of Arts and Social Sciences',
              'Faculty of Education',
              'Faculty of Law',
              'Faculty of Science',
              'Faculty of Theology'
            ];
            foreach ($faculties as $f) {
              $sel = (($_POST['faculty'] ?? '') === $f) ? 'selected' : '';
              echo "<option value=\"" . htmlspecialchars($f) . "\" $sel>" . htmlspecialchars($f) . "</option>";
            }
          ?>
        </select>
      </div>
      <div class="input-group">
        <label for="year">Year of Study</label>
        <input type="number" name="year_of_study" min="1" max="10"
               value="<?= htmlspecialchars($_POST['year_of_study'] ?? '') ?>" required>
      </div>
      <div class="input-group">
        <label for="duration">Course Duration (years)</label>
        <input type="number" name="course_duration" min="1" max="10"
               value="<?= htmlspecialchars($_POST['course_duration'] ?? '') ?>" required>
      </div>
      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" name="password" required>
      </div>
      <div class="button-group">
        <button type="submit">Register</button>
      </div>
    </form>
  </div>

  <!-- Login Form -->
  <div class="container" id="login-container" style="<?= $showLogin ? '' : 'display:none;' ?>">
    <div class="header">STUDENT PORTAL LOGIN</div>
    <form method="POST" action="login.php">
      <div class="input-group">
        <label for="student_id">Student ID</label>
        <input type="text" name="student_id" placeholder="Enter Student ID" required>
      </div>
      <div class="input-group">
        <label for="login-password">Password</label>
        <input type="password" name="password" placeholder="Enter Password" required>
      </div>
      <div class="button-group">
        <button type="submit">Login</button>
      </div>
    </form>
  </div>

</body>
</html>
