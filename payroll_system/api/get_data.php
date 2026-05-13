<?php
header('Content-Type: application/json');
require_once '../config/database.php'; 

try {
    // Gigamit nato ang 'employee' kay mao nay naa sa imong payroll database
    $stmt = $pdo->query("SELECT * FROM employee"); 
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($employees);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>