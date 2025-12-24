<?php

include('../inc/connect2.php');
include('../inc/topbar.php');
session_start();
// Redirect if not logged in
if (empty($_SESSION['login_email'])) {
    header("Location: ../Account/login.php");
    exit;
}

$email = $_SESSION['login_email'];
$sitename = "Hospital Management System";

// Fetch employee info
$user = mysqli_query($conn, "SELECT * FROM tblemployee WHERE email='$email' LIMIT 1");
if ($userData = mysqli_fetch_assoc($user)) {
    $photo = $userData['photo'];
    $employeeID = $userData['id'];
    $fullname = $userData['fullname'];
} else {
    die("Employee not found.");
}

// Attendance Logic
$msg = '';
if (isset($_POST['mark_attendance'])) {
    $today = date('Y-m-d');
    $now = date('H:i:s');

    // Check if attendance exists for today
    $check = mysqli_query($conn, "SELECT * FROM attendances WHERE employeeID='$employeeID' AND date='$today'");

    if (mysqli_num_rows($check) == 0) {
        // Time-in
        $status = ($now > '08:00:00') ? 'Late' : 'On Time';
        mysqli_query($conn, "INSERT INTO attendances (employeeID, date, time_in, status) VALUES ('$employeeID', '$today', '$now', '$status')");
        $msg = "Attendance marked! ($status)";
    } else {
        $row = mysqli_fetch_assoc($check);
        if ($row['time_out'] == NULL) {
            // Time-out
            mysqli_query($conn, "UPDATE attendances SET time_out='$now' WHERE employeeID='$employeeID' AND date='$today'");
            $msg = "Time-out recorded!";
        } else {
            $msg = "Attendance already completed for today.";
        }
    }
}

// Fetch all attendance records for this employee
$attendanceRecords = mysqli_query($conn, "SELECT * FROM attendances WHERE employeeID='$employeeID' ORDER BY date DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employee Attendance | <?php echo $sitename; ?></title>
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
                            <span class="clear"><span class="text-muted text-xs block"><?php echo $email; ?> <b class="caret"></b></span></span>
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

    <!-- Page content -->
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
                            <h5>Mark Your Attendance</h5>
                        </div>

                        <div class="ibox-content">
                            <!-- Attendance Button -->
                            <?php if($msg): ?>
                                <div class="alert alert-info"><?php echo $msg; ?></div>
                            <?php endif; ?>

                            <form method="POST">
                                <button type="submit" name="mark_attendance" class="btn btn-success btn-lg">
                                    <i class="fa fa-clock-o"></i> Mark Attendance
                                </button>
                            </form>

                            <hr>

                            <!-- Attendance Table -->
                            <h5>Your Attendance Records</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time In</th>
                                            <th>Time Out</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($record = mysqli_fetch_assoc($attendanceRecords)): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($record['date']); ?></td>
                                                <td><?php echo htmlspecialchars($record['time_in']); ?></td>
                                                <td><?php echo $record['time_out'] ? htmlspecialchars($record['time_out']) : 'Not yet'; ?></td>
                                                <td><?php echo htmlspecialchars($record['status']); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
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
