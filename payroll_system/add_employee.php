<?php
// Sumpay sa database
$connection = mysqli_connect('localhost', 'root', '', 'payroll', 3306);
if (!$connection) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// I-check kung naay gi-submit nga data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fname'])) {
    
    // Employee Data
    $lname    = $_POST['lname'];
    $fname    = $_POST['fname'];
    $gender   = isset($_POST['gender']) ? $_POST['gender'] : 'Not Specified';
    $type     = isset($_POST['emp_type']) ? $_POST['emp_type'] : 'Full Time';
    $division = $_POST['division'];
    $contact  = isset($_POST['contact']) ? $_POST['contact'] : '';
    $address  = isset($_POST['address']) ? $_POST['address'] : '';
    $email    = isset($_POST['email']) ? $_POST['email'] : '';

    // 1. INSERT SA EMPLOYEE TABLE
    $sql1 = "INSERT INTO employee (lname, fname, gender, emp_type, division, contact, address, email, deduction, overtime, bonus) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 0)";
    $stmt1 = $connection->prepare($sql1);
    $stmt1->bind_param("ssssssss", $lname, $fname, $gender, $type, $division, $contact, $address, $email);

    if ($stmt1->execute()) {
        
        // 2. INSERT SA USER TABLE (INTEGRATION)
        // Gamit ang fname as username ug '1234' as password
        $username = strtolower($fname);
        $password = "1234";
        $fullname = $fname . " " . $lname;
        $role     = "user";

        $sql2 = "INSERT INTO user (username, password, fullname, email, role) VALUES (?, ?, ?, ?, ?)";
        $stmt2 = $connection->prepare($sql2);
        $stmt2->bind_param("sssss", $username, $password, $fullname, $email, $role);
        
        if ($stmt2->execute()) {
            $msg = "Success: Employee and User Account created!";
        } else {
            $msg = "Employee added, but User Table Error: " . $stmt2->error;
        }

        // Response handling
        if (isset($_POST['from_android'])) {
            header('Content-Type: application/json');
            echo json_encode(array("status" => "success", "message" => $msg));
        } else {
            echo "<script>alert('$msg'); window.location.href='home_employee.php?page=emp_list';</script>";
        }

    } else {
        echo "Error in Employee Table: " . $stmt1->error;
    }
} else {
    echo "No data submitted or Method not allowed.";
}
?>