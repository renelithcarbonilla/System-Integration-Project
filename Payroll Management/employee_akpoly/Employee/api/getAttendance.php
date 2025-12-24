<?php
session_start();
require_once(__DIR__ . '/../../database/connect2.php');

$sql = "SELECT * FROM attendances";
$result = mysqli_query($conn, $sql);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
