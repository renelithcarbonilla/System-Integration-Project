<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../config/database.php";

$method = $_SERVER['REQUEST_METHOD'];
$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

$endpoint = end($uri);
$sub = prev($uri);

/* ================================
   API STATUS
================================ */
if ($method === 'GET' && $endpoint === 'index.php') {
    echo json_encode([
        "success" => true,
        "message" => "Payroll API is running"
    ]);
    exit;
}

/* ================================
   EMPLOYEE
================================ */
if ($sub === 'api' && $endpoint === 'employee') {

    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM employee");
        echo json_encode([
            "success" => true,
            "data" => $stmt->fetchAll()
        ]);
        exit;
    }

    if ($method === 'POST') {
        $input = json_decode(file_get_contents("php://input"), true);

        $stmt = $pdo->prepare("
            INSERT INTO employee
            (emp_id, lname, fname, gender, emp_type, division, overtime, bonus, contact, address, email)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $input['emp_id'],
            $input['lname'],
            $input['fname'],
            $input['gender'],
            $input['emp_type'],
            $input['division'],
            $input['overtime'],
            $input['bonus'],
            $input['contact'],
            $input['address'],
            $input['email']
        ]);

        echo json_encode(["success" => true]);
        exit;
    }
}

/* ================================
   DEDUCTIONS
================================ */
if ($sub === 'api' && $endpoint === 'deductions') {

    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM deductions");
        echo json_encode([
            "success" => true,
            "data" => $stmt->fetchAll()
        ]);
        exit;
    }

    if ($method === 'POST') {
        $input = json_decode(file_get_contents("php://input"), true);

        $stmt = $pdo->prepare("
            INSERT INTO deductions
            (deduction_id, healthinsurance, garnishments, other, fica, loans)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $input['deduction_id'],
            $input['healthinsurance'],
            $input['garnishments'],
            $input['other'],
            $input['fica'],
            $input['loans']
        ]);

        echo json_encode(["success" => true]);
        exit;
    }
}

/* ================================
   SALARY
================================ */
if ($sub === 'api' && $endpoint === 'salary') {

    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM salary");
        echo json_encode([
            "success" => true,
            "data" => $stmt->fetchAll()
        ]);
        exit;
    }

    if ($method === 'POST') {
        $input = json_decode(file_get_contents("php://input"), true);

        $stmt = $pdo->prepare("
            INSERT INTO salary (salary_id, salary_rate)
            VALUES (?, ?)
        ");

        $stmt->execute([
            $input['salary_id'],
            $input['salary_rate']
        ]);

        echo json_encode(["success" => true]);
        exit;
    }
}

/* ================================
   OVERTIME
================================ */
if ($sub === 'api' && $endpoint === 'overtime') {

    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM overtime");
        echo json_encode([
            "success" => true,
            "data" => $stmt->fetchAll()
        ]);
        exit;
    }

    if ($method === 'POST') {
        $input = json_decode(file_get_contents("php://input"), true);

        $stmt = $pdo->prepare("
            INSERT INTO overtime (ot_id, rate)
            VALUES (?, ?)
        ");

        $stmt->execute([
            $input['ot_id'],
            $input['rate']
        ]);

        echo json_encode(["success" => true]);
        exit;
    }
}

/* ================================
   PAYROLL (COMPUTED)
================================ */
if ($sub === 'api' && $endpoint === 'payroll') {

    $stmt = $pdo->query("
        SELECT 
            e.emp_id,
            CONCAT(e.fname, ' ', e.lname) AS fullname,
            s.salary_rate,
            IFNULL(o.rate, 0) AS overtime_rate,
            (
                IFNULL(d.healthinsurance,0) +
                IFNULL(d.garnishments,0) +
                IFNULL(d.other,0) +
                IFNULL(d.fica,0) +
                IFNULL(d.loans,0)
            ) AS total_deductions,
            (
                s.salary_rate + IFNULL(o.rate,0) -
                (
                    IFNULL(d.healthinsurance,0) +
                    IFNULL(d.garnishments,0) +
                    IFNULL(d.other,0) +
                    IFNULL(d.fica,0) +
                    IFNULL(d.loans,0)
                )
            ) AS net_salary
        FROM employee e
        LEFT JOIN salary s ON e.emp_id = s.salary_id
        LEFT JOIN overtime o ON e.emp_id = o.ot_id
        LEFT JOIN deductions d ON e.emp_id = d.deduction_id
    ");

    echo json_encode([
        "success" => true,
        "data" => $stmt->fetchAll()
    ]);
    exit;
}

/* ================================
   INVALID ROUTE
================================ */
http_response_code(404);
echo json_encode(["error" => "Endpoint not found"]);
