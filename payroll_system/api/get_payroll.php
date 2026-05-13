<?php
// get_payroll.php
header('Content-Type: application/json'); //API Payroll

$emp_id = $_GET['emp_id'];

$sql = "SELECT * FROM payroll_table WHERE emp_id = '$emp_id' LIMIT 1";
$result = mysqli_query($conn, $sql);

if($row = mysqli_fetch_assoc($result)) {
    echo json_encode([
        "status" => "success",
        "net_pay" => $row['net_pay'],
        "bonus" => $row['bonus'],
        "deductions" => $row['deductions'],
        "overtime" => $row['overtime']
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "No record found"]);
}
?>