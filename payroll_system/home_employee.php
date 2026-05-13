<?php
include('db.php');
include('auth.php');

// Mao ni ang logic para sa pag-add og employee ug automatic user account
if (isset($_POST['submit']) && $_POST['submit'] !== "") {
  $lname    = $_POST['lname'];
  $fname    = $_POST['fname'];
  $gender   = $_POST['gender'];
  $type     = $_POST['emp_type'];
  $division = $_POST['division'];
  $contact  = $_POST['contact'];
  $address  = $_POST['address'];
  $email    = $_POST['email'];

  // 1. INSERT SA EMPLOYEE TABLE
  $stmt = $connection->prepare("INSERT INTO employee (lname, fname, gender, emp_type, division, contact, address, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssssss", $lname, $fname, $gender, $type, $division, $contact, $address, $email);

  if ($stmt->execute()) {
    
    // 2. AUTOMATIC INSERT SA USER TABLE PARA SA ANDROID LOGIN
    $username = strtolower($fname); // Username: first name (lowercase)
    $password = "1234";             // Default password
    $fullname = $fname . " " . $lname;
    $role     = "user";             // Role identifier para sa login logic

    // Gamiton nato ang 'email' ug 'role' column nga atong gi-add sa database
    $stmt_user = $connection->prepare("INSERT INTO user (username, password, fullname, email, role) VALUES (?, ?, ?, ?, ?)");
    $stmt_user->bind_param("sssss", $username, $password, $fullname, $email, $role);
    
    if($stmt_user->execute()){
        echo "<script>alert('Employee and User Account Successfully Added!'); window.location.href='home_employee.php';</script>";
    } else {
        // I-report kung ngano napakyas ang user table (e.g. missing role column)
        echo "<script>alert('Employee Added, but User Account failed: " . $connection->error . "'); window.location.href='home_employee.php';</script>";
    }

  } else {
    echo "<script>alert('An Error Occurred in Employee Table'); window.location.href='home_employee.php';</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Payroll System - Employee Section</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/justified-nav.css" rel="stylesheet">
  <link href="assets/css/dataTables.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
</head>
<body>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h4 class="modal-title" id="logoutLabel">Confirm Exit</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to exit the system?
      </div>
      <div class="modal-footer">
        <a href="login.php" class="btn btn-danger">Yes, Exit</a>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<div class="container">
  <div class="masthead">
    <h3>
      <b><a href="index.php" style="text-decoration:none; color:#3fb1d4;">
        <img src="assets/img.png" alt="lg" width="28px;"> Payroll System
      </a></b>
      <a data-toggle="modal" data-target="#logoutModal" class="btn btn-danger pull-right">
        <i class="fas fa-sign-out-alt"></i> Exit
      </a>
    </h3>
    <nav>
      <ul class="nav nav-justified">
        <li class="active"><a href="">Employee Section <span class="label label-danger"><?php include 'total_count.php'; ?></span></a></li>
        <li><a href="home_deductions.php">Manage Deductions</a></li>
        <li><a href="home_salary.php">Payroll Section</a></li>
      </ul>
    </nav>
  </div>

  <br>
  <div class="well bs-component">
    <fieldset>
      <button type="button" data-toggle="modal" data-target="#addEmployeeModal" class="btn btn-info">
        <i class="fa fa-user-plus"></i> Add Employee Records
      </button>
      <br><br>

      <p align="center"><big><b>Employee Records</b></big></p>
      <div class="table-responsive">
        <table class="table table-hover table-condensed" id="myTable">
          <thead>
            <tr>
              <th><p align="center">Fullname</p></th>
              <th><p align="center">Contact</p></th>
              <th><p align="center">Email</p></th>
              <th><p align="center">Gender</p></th>
              <th><p align="center">Employee Type</p></th>
              <th><p align="center">Department</p></th>
              <th><p align="center">Action</p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Database fetching for the table
            $conn = mysqli_connect('localhost', 'root', '', 'payroll', 3306);
            $query = mysqli_query($conn, "SELECT * FROM employee ORDER BY emp_id ASC");
            while ($row = mysqli_fetch_assoc($query)) {
              ?>
              <tr>
                <td align="center">
                  <a href="view_employee.php?emp_id=<?php echo $row["emp_id"]; ?>" style="color:#0b3f75;">
                    <?php echo $row['fname'] . ' ' . $row['lname']; ?>
                  </a>
                </td>
                <td align="center"><?php echo $row['contact']; ?></td>
                <td align="center"><?php echo $row['email']; ?></td>
                <td align="center"><?php echo $row['gender'] == 'Male' ? '<i class="fas fa-male"></i> M' : '<i class="fas fa-female"></i> F'; ?></td>
                <td align="center"><?php echo $row['emp_type']; ?></td>
                <td align="center"><?php echo $row['division']; ?></td>
                <td align="center">
                  <a class="btn btn-warning" href="view_account.php?emp_id=<?php echo $row["emp_id"]; ?>"><i class="fa fa-file-invoice"></i></a>
                  <a class="btn btn-danger" href="delete.php?emp_id=<?php echo $row["emp_id"]; ?>"><i class="fa fa-trash"></i></a>
                </td>
              </tr>
              <?php
            }
            ?>
          </tbody>
        </table>
      </div>
    </fieldset>
  </div>
</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="addEmployeeLabel">
  <div class="modal-dialog" role="document">
    <form method="POST" action="">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="addEmployeeLabel">Add New Employee</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group"><label>First Name</label><input name="fname" type="text" class="form-control" required></div>
          <div class="form-group"><label>Last Name</label><input name="lname" type="text" class="form-control" required></div>
          <div class="form-group">
            <label>Gender</label>
            <select name="gender" class="form-control" required>
              <option value="">Select...</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
          <div class="form-group"><label>Employee Type</label><input name="emp_type" type="text" class="form-control" required></div>
          <div class="form-group"><label>Department</label><input name="division" type="text" class="form-control" required></div>
          <div class="form-group"><label>Contact</label><input name="contact" type="text" class="form-control" required></div>
          <div class="form-group"><label>Address</label><input name="address" type="text" class="form-control" required></div>
          <div class="form-group"><label>Email</label><input name="email" type="email" class="form-control" required></div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="submit" value="submit" class="btn btn-success">Add Employee</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/dataTables.min.js"></script>
<script>
  $(document).ready(function() {
    $('#myTable').DataTable();
  });
</script>
</body>
</html>