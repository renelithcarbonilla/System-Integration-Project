<?php
$connection = mysqli_connect('localhost', 'root', '', 'payroll', 3306);
if (!$connection) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>