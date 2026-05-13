<?php
error_reporting(0);
header('Content-Type: application/json'); //API Payslip
include('../db.php'); 

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Atong kuhaon ang salary details gikan sa employee table
    // I-match nato ang user.fullname sa employee.lname (parehas sa Profile logic)
    $sql = "SELECT e.lname, e.fname, e.emp_type, e.deduction, e.overtime, e.bonus, e.net_pay 
            FROM user u 
            JOIN employee e ON u.fullname LIKE CONCAT('%', e.lname, '%')
            WHERE u.id = ?";
            
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode([
            "status" => "success",
            "fullname" => $row['fname'] . " " . $row['lname'],
            "deduction" => $row['deduction'],
            "overtime" => $row['overtime'],
            "bonus" => $row['bonus'],
            "net_pay" => $row['net_pay']
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "No payslip found"]);
    }
}
mysqli_close($connection);
?>