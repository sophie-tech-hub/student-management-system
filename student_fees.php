<?php
require 'config.php';

$fees = []; // Initialize $fees as an empty array

if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Fetch student fees
    $sql = "SELECT * FROM fees WHERE student_id = ?";
    $stmt = $connection->prepare($sql);
    
    if ($stmt === false) {
        // Check if preparation failed
        die('Error preparing statement: ' . $connection->error);
    }

    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $fees[] = $row; // Add each fee record to the $fees array
        }
    } else {
        // If no fees are found
        echo "<p>No fee records found for this student.</p>";
    }
} else {
    echo "<p>Student ID not provided.</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Fees</title>
</head>
<body>
    <h2>Student Fee Records</h2>

    <?php if (!empty($fees)): ?>
        <table border="1">
            <tr>
                <th>Fee Amount</th>
                <th>Status</th>
                <th>Due Date</th>
            </tr>
            <?php foreach ($fees as $fee): ?>
            <tr>
                <td><?= htmlspecialchars($fee['fee_amount']) ?></td>
                <td><?= htmlspecialchars($fee['fee_status']) ?></td>
                <td><?= htmlspecialchars($fee['due_date']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No fee records to display.</p>
    <?php endif; ?>

</body>
</html>
