<?php
error_reporting(0);
header('Content-Type: application/json'); //API Profile
include('../db.php'); 

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Atong pangitaon ang user base sa iyang ID, 
    // unya i-match ang fullname ngadto sa employee table
    $sql = "SELECT u.fullname, e.emp_type, e.division, e.contact 
            FROM user u 
            JOIN employee e ON u.fullname = e.lname OR u.fullname LIKE CONCAT('%', e.lname, '%')
            WHERE u.id = ?";
            
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode([
            "status" => "success",
            "fullname" => $row['fullname'],
            "emp_type" => $row['emp_type'],
            "division" => $row['division'],
            "contact" => $row['contact']
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Profile data not found in employee table"]);
    }
}
mysqli_close($connection);
?>