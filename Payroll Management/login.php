<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Payroll System</title>
  <link rel="stylesheet" href="assets/css/login.css">
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="hold-transition login-page">

<?php
require('db.php'); // your db.php file must use mysqli
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare statement to prevent SQL injection
    $stmt = mysqli_prepare($connection, "SELECT * FROM user WHERE username = ? AND password = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit();
        }
    }

    echo "<script>
            alert('Login Invalid, please try again.');
            window.location.href='login.php';
          </script>";
} else {
?>

<br><br><br><br><br><br><br><br>
<div class="container">
  <section id="content">
    <form action="" method="post">
      <h1>Login Panel</h1>
      <div>
        <input name="username" type="text" placeholder="Username" required>
      </div>
      <div>
        <input name="password" type="password" placeholder="Password" required>
      </div>
      <div>
        <input type="submit" value="Login" />
      </div>
    </form>
  </section>
</div>

<?php } ?>
</body>
</html>
