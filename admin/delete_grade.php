<?php
include '../includes/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM grades WHERE id = '$id'";

    if ($connection->query($sql)) {
        echo "Grade deleted successfully!";
        header("Location: grades_admin.php"); // Redirect back to the grades list
    } else {
        echo "Error: " . $connection->error;
    }
}
?>
