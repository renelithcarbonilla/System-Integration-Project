<?php

include('../inc/connect2.php');
include('../inc/topbar.php');
session_start();

if(empty($_SESSION['login_email'])) {
    header("Location: ../Account/login.php");
    exit;
}   

// Get logged-in user info
$email = $_SESSION['login_email'];
$userQuery = mysqli_query($conn, "SELECT * FROM tblemployee WHERE email='$email' LIMIT 1");
if (!$userQuery || mysqli_num_rows($userQuery) == 0) {
    die("User not found.");
}
$user = mysqli_fetch_assoc($userQuery);
$photo = $user['photo'];
$fullname = $user['fullname'];
$role = $user['role'] ?? 'Employee';

// Only Admins can access this page
if ($role != 'Admin') {
    header("Location: ../Employee/attendance.php"); // redirect employees to their own page
    exit;
}

// Optional: Filter by date
$filter_date = '';
if (isset($_POST['filter_date']) && !empty($_POST['filter_date'])) {
    $filter_date = $_POST['filter_date'];
}

// Fetch all attendance records with employee info
$sql = "SELECT a.*, e.fullname, e.email, e.department 
        FROM attendances a 
        JOIN tblemployee e ON a.employeeID = e.id";
if ($filter_date) {
    $sql .= " WHERE a.date = '$filter_date'";
}
$sql .= " ORDER BY a.date DESC, e.fullname ASC";

$attendanceRecords = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Monitoring | Employee Management System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<div id="wrapper">
    <!-- Sidebar -->
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <span>
                            <img src="../<?php echo $photo; ?>" alt="image" width="142" height="153" class="img-circle" />
                        </span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"><span class="text-muted text-xs block"><?php echo $fullname; ?> <b class="caret"></b></span></span>
                        </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                    <?php include('sidebar.php'); ?>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Page Content -->
    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary" href="#"><i class="fa fa-bars"></i></a>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    <li><span class="m-r-sm text-muted welcome-message">Welcome, <?php echo $fullname; ?>!</span></li>
                    <li><a href="logout.php"><i class="fa fa-sign-out"></i> Log out</a></li>
                </ul>
            </nav>
        </div>

        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">

                        <div class="ibox-title">
                            <h5>All Employees Attendance</h5>
                        </div>

                        <div class="ibox-content">
                            <!-- Filter by Date -->
                            <form method="POST" class="form-inline mb-3">
                                <label for="filter_date" class="mr-2">Filter by Date:</label>
                                <input type="date" name="filter_date" id="filter_date" class="form-control mr-2" value="<?php echo htmlspecialchars($filter_date); ?>">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="attendance_monitoring.php" class="btn btn-secondary ml-2">Reset</a>
                            </form>

                            <!-- Attendance Table -->
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Employee Name</th>
                                            <th>Email</th>
                                            <th>Department</th>
                                            <th>Date</th>
                                            <th>Time In</th>
                                            <th>Time Out</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(mysqli_num_rows($attendanceRecords) > 0): ?>
                                            <?php while($record = mysqli_fetch_assoc($attendanceRecords)): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($record['fullname']); ?></td>
                                                    <td><?php echo htmlspecialchars($record['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($record['department']); ?></td>
                                                    <td><?php echo htmlspecialchars($record['date']); ?></td>
                                                    <td><?php echo htmlspecialchars($record['time_in']); ?></td>
                                                    <td><?php echo $record['time_out'] ? htmlspecialchars($record['time_out']) : 'Not yet'; ?></td>
                                                    <td><?php echo htmlspecialchars($record['status']); ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No attendance records found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div><?php include('../inc/footer.php'); ?></div>
        </div>
    </div>
</div>

<script src="js/jquery-2.1.1.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
