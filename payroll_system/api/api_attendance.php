<?php
header('Content-Type: application/json'); //API Attendance
include('../db.php');

$response = array();

if (isset($_POST['user_id']) && isset($_POST['action'])) {
    $user_id = $_POST['user_id'];
    $action = $_POST['action']; // "time_in" o "time_out"
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');

    if ($action == "time_in") {
        // I-check kung naka-Time In na ba siya karong adlawa
        $check = mysqli_query($connection, "SELECT * FROM attendance WHERE user_id = '$user_id' AND attendance_date = '$current_date'");
        
        if (mysqli_num_rows($check) > 0) {
            $response['status'] = "error";
            $response['message'] = "Human na ka naka-Time In karong adlawa!";
        } else {
            $sql = "INSERT INTO attendance (user_id, attendance_date, time_in) VALUES ('$user_id', '$current_date', '$current_time')";
            if (mysqli_query($connection, $sql)) {
                $response['status'] = "success";
                $response['message'] = "Time In Successful: " . $current_time;
            } else {
                $response['status'] = "error";
                $response['message'] = "Failed to record Time In";
            }
        }
    } 
    else if ($action == "time_out") {
        // I-update ang time_out sa iyang record karong adlawa
        $sql = "UPDATE attendance SET time_out = '$current_time' WHERE user_id = '$user_id' AND attendance_date = '$current_date' AND time_out IS NULL";
        
        if (mysqli_query($connection, $sql) && mysqli_affected_rows($connection) > 0) {
            $response['status'] = "success";
            $response['message'] = "Time Out Successful: " . $current_time;
        } else {
            $response['status'] = "error";
            $response['message'] = "Wala kay Time In record o naka-Time Out na ka.";
        }
    }
} else {
    $response['status'] = "error";
    $response['message'] = "Missing parameters.";
}

echo json_encode($response);
mysqli_close($connection);
?>