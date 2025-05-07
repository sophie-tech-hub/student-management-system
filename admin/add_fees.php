<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['faculty']) && isset($_GET['year_of_study'])) {
    $faculty = $_GET['faculty'];
    $year_of_study = $_GET['year_of_study'];

    $sql = "SELECT * FROM students WHERE faculty = ? AND year_of_study = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ii", $faculty, $year_of_study);
    $stmt->execute();
    $students_result = $stmt->get_result();
} else {
    $faculties_query = "SELECT * FROM faculty";
    $faculties_result = $connection->query($faculties_query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Fees</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        h1, h2 {
            text-align: center;
        }

        form, table {
            background: #fff;
            padding: 20px;
            width: 90%;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        select, input[type="number"], input[type="date"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }

        .btn-submit {
            background-color: green;
            color: white;
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
        }

        th {
            background-color: #1E3A8A;
            color: white;
        }

        .btn-save {
            background-color: blue;
            color: white;
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<?php if (!isset($students_result)) { ?>
    <h1>Select Faculty and Year of Study</h1>
    <form action="" method="GET">
        <label for="faculty">Select Faculty: </label>
        <select name="faculty" id="faculty" required>
            <option value="">Select Faculty</option>
            <?php while ($faculty = $faculties_result->fetch_assoc()) { ?>
                <option value="<?php echo $faculty['faculties_id']; ?>"><?php echo $faculty['name']; ?></option>
            <?php } ?>
        </select>

        <label for="year_of_study">Select Year of Study: </label>
        <select name="year_of_study" id="year_of_study" required>
            <option value="">Select Year</option>
            <option value="1">Year 1</option>
            <option value="2">Year 2</option>
            <option value="3">Year 3</option>
            <option value="4">Year 4</option>
        </select>

        <button type="submit" class="btn-submit">View Students</button>
    </form>
<?php } else { ?>
    <h2>Student Fees Entry</h2>
    <form action="save_fees.php" method="POST">
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Fees Paid</th>
                    <th>Date Paid</th>
                    <th>Fees Required</th>
                    <th>Total Paid</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($student = $students_result->fetch_assoc()) { ?>
                    <tr>
                        <td>
                            <input type="hidden" name="student_id[]" value="<?php echo $student['student_id']; ?>">
                            <?php echo $student['student_id']; ?>
                        </td>
                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td><input type="number" name="amount_paid[]" required></td>
                        <td><input type="date" name="payment_date[]" value="<?php echo date('Y-m-d'); ?>" required></td>
                        <td><input type="number" name="fees_required[]" required></td>
                        <td><input type="number" name="total_paid[]" readonly></td>
                        <td><input type="number" name="balance[]" readonly></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <button type="submit" class="btn-save on click" onclick="">Save Fees</button>
    </form>

    <script>
        document.querySelectorAll("input[name='amount_paid[]'], input[name='fees_required[]']").forEach(input => {
            input.addEventListener('input', function () {
                const row = this.closest('tr');
                const paid = parseFloat(row.querySelector("input[name='amount_paid[]']").value) || 0;
                const required = parseFloat(row.querySelector("input[name='fees_required[]']").value) || 0;
                row.querySelector("input[name='total_paid[]']").value = paid;
                row.querySelector("input[name='balance[]']").value = required - paid;
            });
        });
    </script>
<?php } ?>
</body>
</html>
