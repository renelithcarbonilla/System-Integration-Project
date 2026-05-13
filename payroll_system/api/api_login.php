<?php
header('Content-Type: application/json');
include('../db.php'); 

$response = array(); 

if (isset($_POST['username']) && isset($_POST['password'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Atong i-check sa 'user' table
    $sql = "SELECT id, fullname FROM user WHERE username = ? AND password = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $user, $pass);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $response['status'] = "success";
        $response['user_id'] = (int)$row['id'];
        $response['fullname'] = $row['fullname'];
    } else {
        $response['status'] = "error";
        $response['message'] = "Invalid username or password";
    }
} else {
    $response['status'] = "error";
    $response['message'] = "Please provide username and password";
}

echo json_encode($response);
mysqli_close($connection);
?>