<?php
include 'db.php';

// Fetch faculties for the filter
$faculty_query = "SELECT * FROM faculty";
$faculty_result = $connection->query($faculty_query);

// Fetch academics filtered by faculty and year
$faculty_id = isset($_GET['faculty_id']) ? $_GET['faculty_id'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$sql = "SELECT academics.id, students.full_name AS name, academics.subject, academics.grade 
        FROM academics 
        JOIN students ON academics.student_id = students.id
        WHERE students.faculty_id = '$faculty_id' AND students.year = '$year'";
$academics_result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Academics Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        .message {
            text-align: center;
            font-weight: bold;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            margin-top: 20px;
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
            margin-right: 5px;
        }

        .btn-edit {
            background-color: green;
        }

        .btn-delete {
            background-color: red;
        }

        .btn-add {
            background-color: orange;
            margin-bottom: 15px;
            display: inline-block;
        }

        .actions {
            white-space: nowrap;
        }

        form {
            background-color: #fff;
            padding: 20px;
            margin: 0 auto;
            width: 50%;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px 15px;
            background-color: #1E3A8A;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #374EA2;
        }
    </style>
</head>
<body>

    <h2>Manage Academics</h2>

    <!-- Filter Form -->
    <form action="academics_admin.php" method="GET">
        <label for="faculty_id">Faculty:</label>
        <select name="faculty_id">
            <option value="">-- Select Faculty --</option>
            <?php while ($faculty = $faculty_result->fetch_assoc()) { ?>
                <option value="<?php echo $faculty['id']; ?>" <?php echo ($faculty['id'] == $faculty_id) ? 'selected' : ''; ?>>
                    <?php echo $faculty['name']; ?>
                </option>
            <?php } ?>
        </select>

        <label for="year">Year of Study:</label>
        <select name="year">
            <option value="">-- Select Year --</option>
            <option value="1" <?php echo ($year == 1) ? 'selected' : ''; ?>>Year 1</option>
            <option value="2" <?php echo ($year == 2) ? 'selected' : ''; ?>>Year 2</option>
            <option value="3" <?php echo ($year == 3) ? 'selected' : ''; ?>>Year 3</option>
            <option value="4" <?php echo ($year == 4) ? 'selected' : ''; ?>>Year 4</option>
        </select>

        <button type="submit">Filter</button>
    </form>

    <!-- Academics Table -->
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Subject</th>
                <th>Grade</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($academic = $academics_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($academic['name']); ?></td>
                    <td><?php echo htmlspecialchars($academic['subject']); ?></td>
                    <td><?php echo htmlspecialchars($academic['grade']); ?></td>
                    <td class="actions">
                        <a href="edit_academic.php?id=<?php echo $academic['id']; ?>" class="btn btn-edit">Edit</a> |
                        <a href="delete_academic.php?id=<?php echo $academic['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="back-link">
        <a href="add_academic.php" class="btn btn-add">+ Add New Academic Record</a>
    </div>

</body>
</html>
