<?php
session_start();
error_reporting(1);

// Include the connection file
include('../connect.php'); // Adjust path as necessary

// Check if the database connection is established
if (!$dbh) {
    die("Database connection failed!");
}

if (isset($_POST['btnlogin'])) {
  date_default_timezone_set('Asia/Manila');
    $current_date = date('Y-m-d H:i:s');

    $email = $_POST['txtemail'];
    $password = $_POST['txtpassword'];
    $status = 'Active';

    // Use prepared statements to prevent SQL injection
    $sql = "SELECT * FROM tblemployee WHERE email = :email AND password = :password AND status = :status";
    
    // Prepare the statement
    $stmt = $dbh->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':status', $status);

    // Execute the query
    $stmt->execute();

    // Check if any row is returned
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Store session variables
        $_SESSION["email"] = $row['email'];
        $_SESSION["firstname"] = $row['firstname'];
        $_SESSION["phone"] = $row['phone'];

        // Redirect to the home page after successful login
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['error'] = 'Wrong Email Address and Password or Account is Not Activated';
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form | <?php echo $row_website['websitename']; ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="16x16" href="../<?php echo $logo; ?>">
    <style type="text/css">
        .style3 {
            color: #FF0000;
            font-weight: bold;
            font-size: 24px;
        }

        .style4 {
            color: #FF0000
        }
    </style>
</head>

<body class="gray-bg">
    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>
                <h2 class="style3"><?php echo $row_website['url']; ?></h2>
                <h1 class="logo-name"><a href="../index.php"><img src="../img/favicon.png" alt="Amitrade" width="246" height="161" border="0"></a></h1>
            </div>
            <h3 class="style4">Login Form </h3>
            <form class="m-t" role="form" method="POST" action="">
                <div class="form-group">
                    <input type="text" name="txtemail" class="form-control" placeholder="Email" required="">
                </div>
                <div class="form-group">
                    <input type="password" name="txtpassword" class="form-control" placeholder="Password" required="">
                </div>
                <button type="submit" name="btnlogin" class="btn btn-primary block full-width m-b">Login</button>
                <a href="forgot_password.php"><small>Forgot password?</small></a>
                <p class="text-muted text-center">&nbsp;</p>
            </form>
            <p class="m-t"></p>
        </div>
    </div>

    <?php include('../inc/footer.php'); ?>

    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>

</html>
<?php
// Start session
session_start();
error_reporting(1);

// Include the database connection
include('../connect.php');

// Check if the login form is submitted
if (isset($_POST['btnlogin'])) {

    // Get the form input
    $email = $_POST['txtemail'];
    $password = $_POST['txtpassword'];

    // Prepare the SQL query to fetch user details from the database
    $sql = "SELECT * FROM tblemployee WHERE email = :email AND password = :password AND status = 'Active'";
    
    // Prepare and execute the query
    try {
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        // Check if a matching row was found
        if ($stmt->rowCount() > 0) {
            // Fetch the row data
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Store user details in session
            $_SESSION["email"] = $row['email'];
            $_SESSION["phone"] = $row['phone'];
            $_SESSION["firstname"] = $row['firstname'];

            // Record attendance for the user
            $today = date('Y-m-d');
            $time_in = date('Y-m-d H:i:s');
            $check = $dbh->prepare("SELECT * FROM attendances WHERE employeeID = :employeeID AND date = :date");
            $check->bindParam(':employeeID', $employeeID);
            $check->bindParam(':date', $today);
            $check->execute();

            // If attendance hasn't been recorded for today, insert the record
            if ($check->rowCount() == 0) {
                $insertAttendance = $dbh->prepare("INSERT INTO attendances (employeeID, date, time_in) VALUES (:email, :date, :time_in)");
                $insertAttendance->bindParam(':employeeID', $employeeID);
                $insertAttendance->bindParam(':date', $today);
                $insertAttendance->bindParam(':time_in', $time_in);
                $insertAttendance->execute();
            }

            // Redirect to the dashboard or home page after successful login
            header("Location: index.php");
            exit;
        } else {
            // Invalid login credentials
            $_SESSION['error'] = 'Wrong Email Address and Password or Account is Not Activated';
        }
    } catch (PDOException $e) {
        // Handle database error
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form | Employee Management</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/favicon.png">
    <style>
        .style3 {
            color: #FF0000;
            font-weight: bold;
            font-size: 24px;
        }
        .style4 {
            color: #FF0000;
        }
    </style>
</head>
<body class="gray-bg">

    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>
                <h2 class="style3"><?php echo $row_website['url']; ?></h2>
                <h1 class="logo-name"><a href="../index.php"><img src="../img/favicon.png" alt="Logo" width="246" height="161" border="0"></a></h1>
            </div>
            <h3 class="style4">Login Form</h3>
            <form class="m-t" role="form" method="POST" action="">
                <div class="form-group">
                    <input type="text" name="txtemail" class="form-control" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="txtpassword" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" name="btnlogin" class="btn btn-primary block full-width m-b">Login</button>
                <a href="forgot_password.php"><small>Forgot password?</small></a>
            </form>

            <!-- Error Popup -->
            <?php if (!empty($_SESSION['error'])) { ?>
                <div class="popup popup--icon -error js_error-popup popup--visible">
                    <div class="popup__background"></div>
                    <div class="popup__content">
                        <h3 class="popup__content__title"><strong>Error</strong></h3>
                        <p><?php echo $_SESSION['error']; ?></p>
                        <p>
                            <button class="button button--error" data-for="js_error-popup">Close</button>
                        </p>
                    </div>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php } ?>
        </div>
    </div>

    <?php include('../inc/footer.php'); ?>
    
    <!-- Scripts -->
    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="popup_style.css">
    <script>
        var addButtonTrigger = function (el) {
            el.addEventListener('click', function () {
                var popupEl = document.querySelector('.' + el.dataset.for);
                popupEl.classList.toggle('popup--visible');
            });
        };

        Array.from(document.querySelectorAll('button[data-for]')).forEach(addButtonTrigger);
    </script>
</body>
</html>
