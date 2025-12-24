<?php
 $connection = mysqli_connect('localhost', 'root', '', 'payroll_s', 3307);
 if (!$connection) {
     die("Database Connection Failed: " . mysqli_connect_error());
 }
 if (isset($_POST['submit']) != "") {
  $lname      = $_POST['lname'];
  $fname      = $_POST['fname'];
  $gender     = $_POST['gender'];
  $type       = $_POST['emp_type'];
  $division   = $_POST['division'];
  $contact    = $_POST['contact'];
  $address    = $_POST['address'];
  $email      = $_POST['email'];

  $stmt = $connection->prepare("INSERT INTO employee (lname, fname, gender, emp_type, division, contact, address, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssssss", $lname, $fname, $gender, $type, $division, $contact, $address, $email);

  if ($stmt->execute()) {
    ?>
      <script>
        alert('Employee has been added!'); 
        window.location.href='home_employee.php?page=emp_list';
      </script>
    <?php
    } 
  else
    {
      ?>
        <script>
            alert('An Error Occured');
            window.location.href='index.php';
        </script>
      <?php 
    }
  }
?>