<?php
include('db.php');  // Make sure your db.php file correctly establishes a mysqli connection

// Check if the connection was successful
if (!$connection) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Query to get all employee records
$result = mysqli_query($connection, "SELECT * FROM employee");

// Check if there are any rows
$rows = mysqli_num_rows($result);

echo $rows;  // Output the number of rows
?>
