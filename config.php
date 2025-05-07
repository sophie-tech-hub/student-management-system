<?php

$servername = "localhost";
$username = "root"; // Replace with your database username
$password = "root";     // Replace with your database password
$dbname = "student_management_system"; // Replace with your database name;

///$connection = new mysqli($servername, $username, $password, $dbname);
$connection = new mysqli('127.0.0.1', 'root', '', 'student_management_system', 3307);
if ($connection->connect_error) {
     die("Connection failed: " . $connection->connect_error);
 } else {
     echo "";
 }
?>
