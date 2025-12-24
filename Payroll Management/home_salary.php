<?php
/* ===============================
   1️⃣ AUTH + DB
================================= */
include("auth.php");
include("db.php");

/* ===============================
   2️⃣ FETCH ATTENDANCE FROM API
================================= */
$url = "http://localhost/employee_akpoly/Employee/api/getAttendance.php";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-API-KEY: ATTENDANCE123"
]);

$response = curl_exec($ch);

if ($response === false) {
    die("CURL ERROR: " . curl_error($ch));
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode != 200) {
    die("API Error. Status Code: " . $httpCode);
}

$attendance = json_decode($response, true);

/* ===============================
   3️⃣ INSERT ATTENDANCE INTO PAYROLL
================================= */
$conn = new mysqli("localhost", "root", "", "payroll_s", 3307);

if ($conn->connect_error) {
    die("DB Connection Failed");
}

if (is_array($attendance)) {
    foreach ($attendance as $row) {

        $rate_per_hour = 100;
        $salary = $row['total_hours'] * $rate_per_hour;
        $overtime = $row['overtime_hours'] * ($rate_per_hour * 1.25);
        $gross = $salary + $overtime;

        // OPTIONAL: prevent duplicate insert
        $check = $conn->prepare(
            "SELECT id FROM payroll WHERE employee_id = ? AND payroll_date = ?"
        );
        $check->bind_param("is", $row['employee_id'], $row['date']);
        $check->execute();
        $check->store_result();

        if ($check->num_rows == 0) {
            $stmt = $conn->prepare("
                INSERT INTO payroll 
                (employee_id, payroll_date, total_hours, overtime_hours, gross_salary)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "isddd",
                $row['employee_id'],
                $row['date'],
                $row['total_hours'],
                $row['overtime_hours'],
                $gross
            );

            $stmt->execute();
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
  <head>

    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Bootstrap, a sleek, intuitive, and powerful mobile first front-end framework for faster and easier web development.">
    <meta name="keywords" content="HTML, CSS, JS, JavaScript, framework, bootstrap, front-end, frontend, web development">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">

    <title>Payroll System</title>

    <link href="assets/css/justified-nav.css" rel="stylesheet">


    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <!-- <link href="data:text/css;charset=utf-8," data-href="assets/css/bootstrap-theme.min.css" rel="stylesheet" id="bs-theme-stylesheet"> -->
    <!-- <link href="assets/css/docs.min.css" rel="stylesheet"> -->
    <link href="assets/css/search.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="assets/css/styles.css" /> -->
    <link rel="stylesheet" type="text/css" href="assets/css/dataTables.min.css">

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
            <li>
              <a href="home_employee.php">Employee Section <span class="label label-primary"><?php include 'total_count.php'?></span></a>
            </li>
            <li>
              <a href="home_deductions.php">Manage Deductions</a>
            </li>
            <li class="active">
              <a href="">Payroll Section</a>
            </li>
          </ul>
        </nav>
      </div>

        <br>
          <div class="well bs-component">
            <form class="form-horizontal">
              <fieldset>
                <button type="button" data-toggle="modal" data-target="#overtime" class="btn btn-warning" style="border-radius:0%"> <i class="fa fa-hourglass-half"></i> Update Overtime Rate</button>
                <button type="button" data-toggle="modal" data-target="#salary" class="btn btn-primary" style="border-radius:0%"><i class="fa fa-money-bill-alt"></i> Update Salary Rate</button>
                <p class="pull-right">Overtime Rate (Per Hour) <big><b><?php echo $rate; ?>.00</b></big></p><br>
                <p class="pull-right">Salary Rate <big><b><?php echo $salary; ?>.00</b></big></p>
                <p align="center"><big><b>Payroll</b></big></p>
                <div class="table-responsive">
                  <form method="post" action="" >
                    <table class="table table-hover table-condensed table-responsive" id="myTable">
                      <!-- <h3><b>Ordinance</b></h3> -->
                      <thead>
                        <tr>
                          <th><p align="center">Fullname</p></th>
                          <th><p align="center">Overtime</p></th>
                          <th><p align="center">Bonus</p></th>
                          <th><p align="center">Deductions</p></th>
                          <th><p align="center">Net Pay</p></th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                        // Get overtime rate
                        $query = mysqli_query($connection, "SELECT * FROM overtime LIMIT 1");
                        $row = mysqli_fetch_assoc($query);
                        $rate = $row['rate'];

                        // Get salary rate
                        $query = mysqli_query($connection, "SELECT * FROM salary LIMIT 1");
                        $row = mysqli_fetch_assoc($query);
                        $salary_rate = $row['salary_rate'];

                        // Get employee data and calculate net pay
                        $query = mysqli_query($connection, "SELECT * FROM employee");
                        while($row = mysqli_fetch_array($query)) {
                            $lname     = $row['lname'];
                            $fname     = $row['fname'];
                            $deduction = $row['deduction'];
                            $overtime  = $row['overtime'];
                            $bonus     = $row['bonus'];

                            $over    = $overtime * $rate;
                            $income  = $over + $bonus + $salary_rate;
                            $netpay  = $income - $deduction;

                            // You can now echo or use these variables as needed
                            echo "<p>$fname $lname - Net Pay: ₱" . number_format($netpay, 2) . "</p>";
                        }
                      ?>
                        <tr>
                          <td align="center"><?php echo $fname?> <?php echo $lname?></td>
                          <td align="center"><big><b><?php echo $overtime?></b></big> hrs</td>
                          <td align="center"><big><b><?php echo $bonus?></b></big>.00</td>
                          <td align="center"><big><b><?php echo $deduction?></b></big>.00</td>
                          <td align="center"><big><b><?php echo 'Rs.' .$netpay. ''?></b></big>.00</td>
                        </tr>
                      </tbody>

                        
                    </table>
                  </form>
                </div>
              </fieldset>
            </form>
          </div>

      <!-- this modal is for OVERTIME -->
      <div class="modal fade" id="overtime" role="dialog">
        <div class="modal-dialog modal-sm">
        
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header" style="padding:20px 50px;">
              <button type="button" class="close" data-dismiss="modal" title="Close">&times;</button>
              <h3 align="center">Enter <big><b>Overtime</b></big> rate (per hour):</h3>
            </div>
            <div class="modal-body" style="padding:40px 50px;">

              <form class="form-horizontal" action="update_overtime.php" name="form" method="post">
                <div class="form-group">
                    <input type="text" name="rate" class="form-control" value="<?php echo $rate; ?>" required="required">
                </div>

                <div class="form-group">
                    <input type="submit" name="submit" class="btn btn-success" value="Submit">
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>

      <!-- this modal is for SALARY -->
      <div class="modal fade" id="salary" role="dialog">
        <div class="modal-dialog modal-sm">
        
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header" style="padding:20px 50px;">
              <button type="button" class="close" data-dismiss="modal" title="Close">&times;</button>
              <h3 align="center">Enter <big><b>Salary</b></big> rate:</h3>
            </div>
            <div class="modal-body" style="padding:40px 50px;">

              <form class="form-horizontal" action="update_salary.php" name="form" method="post">
                <div class="form-group">
                    <input type="text" name="salary_rate" class="form-control" value="<?php echo $salary; ?>" required="required">
                </div>

                <div class="form-group">
                    <input type="submit" name="submit" class="btn btn-success" value="Submit">
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>

      <!-- this modal is for my Colins -->
      <div class="modal fade" id="colins" role="dialog">
        <div class="modal-dialog modal-sm">
              
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header" style="padding:20px 50px;">
              <button type="button" class="close" data-dismiss="modal" title="Close">&times;</button>
              <h3 align="center">You are logged in as <b><?php echo $_SESSION['username']; ?></b></h3>
            </div>
            <div class="modal-body" style="padding:40px 50px;">
              <div align="center">
                <a href="logout.php" class="btn btn-block btn-danger">Logout</a>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- <script src="assets/js/docs.min.js"></script> -->
    <script src="assets/js/search.js"></script>
    <script type="text/javascript" charset="utf-8" language="javascript" src="assets/js/dataTables.min.js"></script>

    <!-- FOR DataTable -->
    <script>
      {
        $(document).ready(function()
        {
          $('#myTable').DataTable();
        });
      }
    </script>

    <!-- this function is for modal -->
    <script>
      $(document).ready(function()
      {
        $("#myBtn").click(function()
        {
          $("#myModal").modal();
        });
      });
    </script>

  </body>
</html>