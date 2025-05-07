<?php
session_start();
require 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($student_id) || empty($password)) {
        $message = "Student ID and Password are required.";
    } else {
        // Look for the student by student_id
        $sql = "SELECT student_id, full_name, password FROM students WHERE student_id = ?";
        if ($stmt = $connection->prepare($sql)) {
            $stmt->bind_param('s', $student_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($user = $result->fetch_assoc()) {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Login successful
                    $_SESSION['student_id'] = $user['student_id'];
                    $_SESSION['student_name'] = $user['full_name'];
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $message = "Incorrect password.";
                }
            } else {
                $message = "Student ID not found.";
            }
            $stmt->close();
        } else {
            $message = "Query failed: " . $connection->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <style>
    body {
      background-color: black;
      color: white;
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      background-color: #333;
      padding: 30px;
      border-radius: 10px;
      width: 350px;
      box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
      border: 2px solid grey;
    }
    .input-group {
      margin-bottom: 15px;
      display: flex;
      flex-direction: column;
      text-align: left;
    }
    input {
      padding: 10px;
      border: none;
      border-radius: 5px;
      background-color: #555;
      color: white;
    }
    button {
      padding: 10px;
      border: none;
      border-radius: 5px;
      background-color: #1E3A8A;
      color: white;
      cursor: pointer;
    }
    .error {
      color: #ff4c4c;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Student Login</h2>
  <?php if (!empty($message)): ?>
    <p class="error"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>
  <form method="POST" action="">
    <div class="input-group">
      <label>Student ID</label>
      <input type="text" name="student_id" placeholder="Enter Student ID" required>
    </div>
    <div class="input-group">
      <label>Password</label>
      <input type="password" name="password" placeholder="Enter Password" required>
    </div>
    <button type="submit">Login</button>
  </form>
</div>

</body>
</html>
