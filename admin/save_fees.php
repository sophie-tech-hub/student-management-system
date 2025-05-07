<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'], $_POST['faculties_id'])) {
    $student_ids = $_POST['student_id'];
    $amounts_paid = $_POST['amount_paid'];
    $payment_dates = $_POST['payment_date'];
    $faculties_ids = $_POST['faculties_id'];
    $fees_required = $_POST['fees_required'];
    $totals_paid = $_POST['total_paid'];
    $balances = $_POST['balance'];

    // Basic validation to ensure arrays are aligned
    $count = count($student_ids);
    if (
        $count !== count($amounts_paid) ||
        $count !== count($payment_dates) ||
        $count !== count($faculties_ids) ||
        $count !== count($fees_required) ||
        $count !== count($totals_paid) ||
        $count !== count($balances)
    ) {
        die("Mismatch in input data. Please check your form.");
    }

    // Prepare SQL statement to insert into the fees table
    $stmt_fees = $connection->prepare("INSERT INTO fees (student_id, amount_paid, payment_date, faculties_id, fees_required, total_paid, balance) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt_fees) {
        die("Prepare failed: " . $connection->error);
    }

    // Loop through all submitted rows
    for ($i = 0; $i < $count; $i++) {
        $student_id = $student_ids[$i];
        $amount_paid = $amounts_paid[$i];
        $payment_date = $payment_dates[$i];
        $faculty_id = $faculties_ids[$i];
        $fee_required = $fees_required[$i];
        $total_paid = $totals_paid[$i];
        $balance = $balances[$i];

        // Bind parameters and execute
        $stmt_fees->bind_param("idssddd", $student_id, $amount_paid, $payment_date, $faculty_id, $fee_required, $total_paid, $balance);

        if ($stmt_fees->execute()) {
            echo "Fee record saved for Student ID: $student_id<br>";
        } else {
            echo "Error saving fee for Student ID: $student_id - " . $stmt_fees->error . "<br>";
        }
    }

    echo "<strong> All fees processed successfully!</strong>";
} else {
    echo " Invalid request. Required fields are missing.";
}
?>
