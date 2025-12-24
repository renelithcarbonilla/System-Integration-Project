<?php
require_once 'config/database.php';
require_once 'api_client.php';

// Call an API endpoint
$response = callAPI("GET", "http://localhost/payroll_system/api/employee");
print_r($response);

// Fetch from database directly
$stmt = $pdo->query("SELECT * FROM deductions");
$deductions = $stmt->fetchAll();
print_r($deductions);
