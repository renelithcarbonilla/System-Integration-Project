<?php
  include("db.php"); 
  include("auth.php");

  // 1. Kuhaon ang default settings para sa deductions (Settings table)
  $sql_deduct = mysqli_query($connection, "SELECT * FROM deductions WHERE deduction_id='1'");
  while($row_d = mysqli_fetch_array($sql_deduct))
  {
    $healthinsurance = $row_d['healthinsurance'];
    $garnishments = $row_d['garnishments'];
    $others = $row_d['others'];
    $fica = $row_d['fica'];
    $loans = $row_d['loans'];
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payroll System - View Account</title>
    <link href="assets/css/justified-nav.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
    <link href="assets/css/search.css" rel="stylesheet">
  </head>
  <body>

    <div class="container">
      <div class="masthead">
        <h3>
          <b><a href="index.php" style="text-decoration:none; color:#3fb1d4;"><img src="assets/img.png" alt="lg" width="28px;"> Payroll System</a></b>
          <a data-toggle="modal" style="text-decoration:none; color:#3fb1d4;" href="#colins" class="pull-right"><b><button class="btn btn-danger" style="border-radius: 0%;"><i class="fas fa-sign-out-alt"></i> Exit</button></b></a>
        </h3>
        <nav>
          <ul class="nav nav-justified" style="border-radius:0%">
            <li class="active"><a href="home_employee.php">Employee Section <span class="label label-primary"><?php include 'total_count.php'?></span></a></li>
            <li><a href="home_deductions.php">Manage Deductions</a></li>
            <li><a href="home_salary.php">Payroll Section</a></li>
          </ul>
        </nav>
      </div><br><br>

      <?php
        require("db.php");
        // Kuhaon ang emp_id gikan sa URL
        $id = $_REQUEST['emp_id'] ?? null;

        if ($id) {
            // 2. Fetch Employee Data
            $stmt = $connection->prepare("SELECT * FROM employee WHERE emp_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $employeeData = $stmt->get_result()->fetch_assoc();

            if (!$employeeData) {
                echo "<div class='alert alert-danger'>Employee not found!</div>";
                exit;
            }

            // 3. Fetch Overtime Rate
            $stmtOvertime = $connection->prepare("SELECT rate FROM overtime WHERE ot_id = ?");
            $stmtOvertime->bind_param("i", $employeeData['ot_id']);
            $stmtOvertime->execute();
            $overtimeRate = $stmtOvertime->get_result()->fetch_assoc()['rate'] ?? 0;

            // 4. Fetch Salary Rate (36500 base sa imong screenshot)
            $stmtSalary = $connection->prepare("SELECT salary_rate FROM salary WHERE salary_id = ?");
            $stmtSalary->bind_param("i", $employeeData['salary_id']);
            $stmtSalary->execute();
            $salaryRate = $stmtSalary->get_result()->fetch_assoc()['salary_rate'] ?? 0;

            // 5. AUTOMATION: Compute Rendered Hours from Attendance Table (user_id column)
            $stmtAttendance = $connection->prepare("
                SELECT SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) as rendered_hours 
                FROM attendance 
                WHERE user_id = ?
            ");
            $stmtAttendance->bind_param("i", $id);
            $stmtAttendance->execute();
            $attendanceData = $stmtAttendance->get_result()->fetch_assoc();
            
            $rendered_hours = $attendanceData['rendered_hours'] ?? 0;
            
            // Logic: Kung sobra sa 8 hours ang total work, ang sobra maoy overtime
            $standard_hours = 8; 
            $auto_overtime = ($rendered_hours > $standard_hours) ? ($rendered_hours - $standard_hours) : 0;

            // 6. Final Calculations for Net Pay
            $overtime_val = ($employeeData['overtime'] > 0) ? $employeeData['overtime'] : $auto_overtime;
            $overtime_pay = $overtime_val * $overtimeRate;
            $bonus = $employeeData['bonus'];
            $deduction = $employeeData['deduction'];
            
            $income = $overtime_pay + $bonus + $salaryRate;
            $netpay = $income - $deduction;

            $row = $employeeData;

        } else {
            echo "<div class='alert alert-danger'>Invalid ID!</div>";
            exit;
        }
      ?>

      <!-- HTML Form -->
      <form class="form-horizontal" action="update_account.php" method="post">
        <input type="hidden" name="new" value="1" />
        <input name="id" type="hidden" value="<?php echo $row['emp_id']; ?>" />
        
        <div class="form-group">
          <div class="col-sm-offset-4 col-sm-4 text-center">
            <h2><?php echo $row['lname'] . ", " . $row['fname']; ?></h2>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-5 control-label">Deduction/s:</label>
          <div class="col-sm-4">
            <select name="deduction" class="form-control" required>
              <option value="<?php echo $row['deduction']; ?>">Current: ₱<?php echo $row['deduction']; ?></option>
              <option value="<?php echo $healthinsurance; ?>">Health-Insurance (₱<?php echo $healthinsurance; ?>)</option>
              <option value="<?php echo $garnishments; ?>">Garnishments (₱<?php echo $garnishments; ?>)</option>
              <option value="<?php echo $others; ?>">Others (₱<?php echo $others; ?>)</option>
              <option value="<?php echo $fica; ?>">FICA (₱<?php echo $fica; ?>)</option>
              <option value="<?php echo $loans; ?>">Loans (₱<?php echo $loans; ?>)</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-5 control-label">Overtime (hours):</label>
          <div class="col-sm-4">
            <input type="text" name="overtime" class="form-control" value="<?php echo $overtime_val; ?>" required>
            <span class="text-info small"><b>Info:</b> Total hours from app: <?php echo $rendered_hours; ?> hrs</span>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-5 control-label">Bonus Amount:</label>
          <div class="col-sm-4">
            <input type="text" name="bonus" class="form-control" value="<?php echo $row['bonus']; ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-12 text-center control-label" style="font-size: 24px;">
            <b>Net Pay: <span class="text-danger">₱<?php echo number_format($netpay, 2); ?></span></b>
          </label>
        </div><br>

        <div class="form-group text-center">
          <input type="submit" name="submit" style="border-radius:0%" value="Update" class="btn btn-danger btn-lg">
          <a href="home_employee.php" style="border-radius:0%" class="btn btn-primary btn-lg">Cancel</a>
        </div>
      </form>

      <!-- Logout Modal -->
      <div class="modal fade" id="colins" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 align="center">Logged in as: <b><?php echo $_SESSION['username']; ?></b></h4>
            </div>
            <div class="modal-body text-center">
              <a href="logout.php" class="btn btn-block btn-danger">Logout</a>
            </div>
          </div>
        </div>
      </div>

    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
  </body>
</html>