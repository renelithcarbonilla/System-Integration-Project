<?php 
include('../inc/topbar.php');
include('../inc/dbconn.php');

// Check if connection to the database is successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all employees
$employees = mysqli_query($conn, "SELECT * FROM tblemployee ORDER BY fullname ASC");

// Check if $employees query was successful
if (!$employees) {
    die("Error fetching employees: " . mysqli_error($conn));
}

// Ensure required session variables are set
$sitename = $_SESSION['sitename'] ?? 'Employee Management System';
$photo = isset($_SESSION['photo']) ? $_SESSION['photo'] : 'default.jpg';
$email = isset($_SESSION['login_email']) ? $_SESSION['login_email'] : 'guest@example.com';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance | <?php echo $sitename; ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div id="wrapper">
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

        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary" href="#"><i class="fa fa-bars"></i></a>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li><span class="m-r-sm text-muted welcome-message">Welcome to <?php echo $sitename; ?></span></li>
                        <li><a href="logout.php"><i class="fa fa-sign-out"></i> Log out</a></li>
                    </ul>
                </nav>
            </div>

            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox">
                            <div class="ibox-title"><h5>All Employees</h5></div>
                            <div class="ibox-content">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Photo</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Department</th>
                                                <th>Role</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($employees)) : ?>
                                                <tr>
                                                    <td>
                                                        <?php if (!empty($row['photo'])) : ?>
                                                            <img src="../<?php echo htmlspecialchars($row['photo']); ?>" width="60" height="60" class="img-circle">
                                                        <?php else : ?>
                                                            <span>No Photo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['department']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                                                    <td>
                                                        <a href="edit_employee.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                                                        <a href="delete_employee.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <a href="register_employee.php" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Employee</a>
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
