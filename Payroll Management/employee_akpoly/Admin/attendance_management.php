<?php
include('../inc/topbar.php');
include('/database/connect.php');

if (empty($_SESSION['admin-username'])) {
    header("Location: login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Attendance Record | <?php echo $sitename; ?></title>
  <link rel="icon" type="image/png" sizes="16x16" href="../<?php echo $logo; ?>">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Home</a>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
      <img src="../<?php echo $logo2; ?>" alt="Logo" width="150" height="130" style="opacity: .8">
    </a>
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../<?php echo $row_admin['photo']; ?>" width="140" height="141" class="img-circle elevation-2">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo $row_admin['fullname']; ?></a>
        </div>
      </div>
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <?php include('sidebar.php'); ?>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1 class="m-0 text-dark">Attendance Record</h1></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Attendance Record</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <table width="100%" border="0" align="center">
            <tr>
              <td>
                <div class="card">
                  <div class="card-header"><h3 class="card-title">Staff Attendance</h3></div>
                  <div class="card-body">
                    <table class="table table-bordered table-striped" id="example1">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Photo</th>
                          <th>Staff Name</th>
                          <th>Date</th>
                          <th>Time-In</th>
                          <th>Time-Out</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                     try {
                        $data = $dbh->query("SELECT tblemployee.fullname, tblemployee.photo, attendances.* 
                                             FROM tblemployee 
                                             INNER JOIN attendances ON tblemployee.id = attendances.employeeID 
                                             ORDER BY attendances.employeeID DESC")->fetchAll();
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                      $cnt = 1;
                      foreach ($data as $row) {
                      ?>
                        <tr>
                          <td><?php echo $cnt++; ?></td>
                          <td><img src="../<?php echo $row['photo']; ?>" width="50" height="43"></td>
                          <td><?php echo $row['fullname']; ?></td>
                          <td><?php echo $row['date']; ?></td>
                          <td><?php echo $row['time_in']; ?></td>
                          <td><?php echo $row['time_out']; ?></td>
                          <td>
                            <?php
                              if ($row['status'] == 'Present') {
                                  echo '<span class="badge badge-success">Present</span>';
                              } elseif ($row['status'] == 'Absent') {
                                  echo '<span class="badge badge-danger">Absent</span>';
                                } elseif ($row['status'] == 'Late') {
                                    echo '<span class="badge badge-danger">Late</span>';
                              } else {
                                  echo '<span class="badge badge-warning">Unknown</span>';
                              }
                            ?>
                          </td>
                        </tr>
                      <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer">
    <?php include('../inc/footer.php'); ?>
  </footer>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
  });
</script>
</body>
</html>
