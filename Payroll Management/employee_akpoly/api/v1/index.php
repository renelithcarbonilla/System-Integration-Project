<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../../config/database.php';

class SimplePayrollAPI {
    private $conn;
    
    public function __construct() {
        $db = Database::getInstance();
        $this->conn = $db->getConnection();
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Simple routing
        if (strpos($path, '/api/employees') !== false) {
            $this->handleEmployees($method);
        } elseif (strpos($path, '/api/attendances') !== false) {
            $this->handleAttendances($method);
        } elseif (strpos($path, '/api/payroll') !== false) {
            $this->handlePayroll($method);
        } elseif ($path === '/' || $path === '/api') {
            $this->showApiInfo();
        } else {
            $this->sendResponse(404, ['error' => 'Endpoint not found. Available: /api/employees, /api/attendances, /api/payroll']);
        }
    }
    
    private function handleEmployees($method) {
        switch ($method) {
            case 'GET':
                $this->getEmployees();
                break;
            case 'POST':
                $this->createEmployee();
                break;
            default:
                $this->sendResponse(405, ['error' => 'Method not allowed']);
        }
    }
    
    private function handleAttendances($method) {
        switch ($method) {
            case 'GET':
                $this->getAttendances();
                break;
            case 'POST':
                $this->createAttendance();
                break;
            default:
                $this->sendResponse(405, ['error' => 'Method not allowed']);
        }
    }
    
    private function handlePayroll($method) {
        if ($method === 'GET') {
            $this->calculatePayroll();
        } else {
            $this->sendResponse(405, ['error' => 'Method not allowed']);
        }
    }
    
    private function getEmployees() {
        try {
            $stmt = $this->conn->query("
                SELECT id, employeeID, fullname, sex, email, dob, phone, 
                       dept, employee_type, date_appointment, 
                       basic_salary, gross_pay, status, leave_status
                FROM tblemployee
                ORDER BY fullname
            ");
            $employees = $stmt->fetchAll();
            
            $this->sendResponse(200, [
                'success' => true,
                'count' => count($employees),
                'data' => $employees
            ]);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Failed to fetch employees: ' . $e->getMessage()]);
        }
    }
    
    private function createEmployee() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                $this->sendResponse(400, ['error' => 'Invalid JSON data']);
                return;
            }
            
            // Required fields
            $required = ['employeeID', 'fullname', 'email', 'dept'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    $this->sendResponse(400, ['error' => "Missing required field: $field"]);
                    return;
                }
            }
            
            $sql = "INSERT INTO tblemployee 
                    (employeeID, fullname, password, sex, email, dob, phone, address, 
                     qualification, dept, employee_type, date_appointment, basic_salary, 
                     gross_pay, status, leave_status, photo) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $success = $stmt->execute([
                $data['employeeID'] ?? '',
                $data['fullname'] ?? '',
                $data['password'] ?? '123456', // Default password
                $data['sex'] ?? 'Male',
                $data['email'] ?? '',
                $data['dob'] ?? date('Y-m-d'),
                $data['phone'] ?? '',
                $data['address'] ?? '',
                $data['qualification'] ?? '',
                $data['dept'] ?? '',
                $data['employee_type'] ?? 'Full-time',
                $data['date_appointment'] ?? date('Y-m-d'),
                $data['basic_salary'] ?? 0,
                $data['gross_pay'] ?? 0,
                $data['status'] ?? 'Active',
                $data['leave_status'] ?? 'Available',
                $data['photo'] ?? ''
            ]);
            
            if ($success) {
                $this->sendResponse(201, [
                    'success' => true,
                    'message' => 'Employee created successfully',
                    'id' => $this->conn->lastInsertId()
                ]);
            } else {
                $this->sendResponse(500, ['error' => 'Failed to create employee']);
            }
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Server error: ' . $e->getMessage()]);
        }
    }
    
    private function getAttendances() {
        try {
            $date = $_GET['date'] ?? date('Y-m-d');
            
            $stmt = $this->conn->prepare("
                SELECT a.*, e.fullname 
                FROM attendances a 
                LEFT JOIN tblemployee e ON a.employeeID = e.employeeID 
                WHERE a.date = ?
                ORDER BY a.time_in
            ");
            $stmt->execute([$date]);
            $attendances = $stmt->fetchAll();
            
            $this->sendResponse(200, [
                'success' => true,
                'date' => $date,
                'count' => count($attendances),
                'data' => $attendances
            ]);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Failed to fetch attendances: ' . $e->getMessage()]);
        }
    }
    
    private function createAttendance() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $required = ['employeeID', 'date', 'time_in'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    $this->sendResponse(400, ['error' => "Missing required field: $field"]);
                    return;
                }
            }
            
            // Check if employee exists
            $checkStmt = $this->conn->prepare("SELECT id FROM tblemployee WHERE employeeID = ?");
            $checkStmt->execute([$data['employeeID']]);
            
            if ($checkStmt->rowCount() === 0) {
                $this->sendResponse(400, ['error' => 'Employee not found']);
                return;
            }
            
            $sql = "INSERT INTO attendances (employeeID, date, time_in, time_out, status) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $success = $stmt->execute([
                $data['employeeID'],
                $data['date'],
                $data['time_in'],
                $data['time_out'] ?? null,
                $data['status'] ?? 'Present'
            ]);
            
            if ($success) {
                $this->sendResponse(201, [
                    'success' => true,
                    'message' => 'Attendance recorded successfully'
                ]);
            } else {
                $this->sendResponse(500, ['error' => 'Failed to record attendance']);
            }
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Server error: ' . $e->getMessage()]);
        }
    }
    
    private function calculatePayroll() {
        try {
            $month = $_GET['month'] ?? date('Y-m');
            $employeeID = $_GET['employee'] ?? null;
            
            // Base query
            $sql = "
                SELECT 
                    e.employeeID,
                    e.fullname,
                    e.dept,
                    e.basic_salary,
                    e.gross_pay,
                    COUNT(a.id) as days_present,
                    e.basic_salary / 22 as daily_rate,
                    (e.basic_salary / 22) * COUNT(a.id) as earned_salary,
                    e.gross_pay - (e.basic_salary / 22 * COUNT(a.id)) as deductions
                FROM tblemployee e
                LEFT JOIN attendances a ON e.employeeID = a.employeeID 
                    AND DATE_FORMAT(a.date, '%Y-%m') = ?
                    AND a.status = 'Present'
                WHERE e.status = 'Active'
            ";
            
            $params = [$month];
            
            if ($employeeID) {
                $sql .= " AND e.employeeID = ?";
                $params[] = $employeeID;
            }
            
            $sql .= " GROUP BY e.id ORDER BY e.fullname";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $payroll = $stmt->fetchAll();
            
            // Calculate totals
            $total_basic = 0;
            $total_earned = 0;
            $total_deductions = 0;
            
            foreach ($payroll as $item) {
                $total_basic += $item['basic_salary'];
                $total_earned += $item['earned_salary'];
                $total_deductions += $item['deductions'];
            }
            
            $this->sendResponse(200, [
                'success' => true,
                'month' => $month,
                'employee_filter' => $employeeID ?? 'All',
                'summary' => [
                    'total_employees' => count($payroll),
                    'total_basic_salary' => $total_basic,
                    'total_earned_salary' => $total_earned,
                    'total_deductions' => $total_deductions,
                    'net_payroll' => $total_earned
                ],
                'data' => $payroll
            ]);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Failed to calculate payroll: ' . $e->getMessage()]);
        }
    }
    
    private function showApiInfo() {
        $this->sendResponse(200, [
            'api' => 'Simple Payroll API',
            'version' => '1.0',
            'endpoints' => [
                'GET /api/employees' => 'List all employees',
                'POST /api/employees' => 'Add new employee (JSON body)',
                'GET /api/attendances' => 'Get attendance for today (add ?date=YYYY-MM-DD for specific date)',
                'POST /api/attendances' => 'Record attendance (JSON body)',
                'GET /api/payroll' => 'Calculate payroll for current month (add ?month=YYYY-MM&employee=ID for filters)'
            ],
            'examples' => [
                'create_employee' => '{"employeeID":"EMP001","fullname":"John Doe","email":"john@test.com","dept":"IT","basic_salary":50000}',
                'create_attendance' => '{"employeeID":"EMP001","date":"2024-01-15","time_in":"08:00:00","time_out":"17:00:00"}'
            ]
        ]);
    }
    
    private function sendResponse($code, $data) {
        http_response_code($code);
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
}

// Handle the request
$api = new SimplePayrollAPI();
$api->handleRequest();
?>