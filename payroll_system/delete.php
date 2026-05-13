<?php 
    require('db.php');

    if (isset($_GET['emp_id']) && is_numeric($_GET['emp_id'])) {
        $id = $_GET['emp_id'];

        // Use prepared statement to prevent SQL injection
        $stmt = $connection->prepare("DELETE FROM employee WHERE emp_id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: home_employee.php");
            exit();
        } else {
            echo "Error deleting employee.";
        }
    } else {
        echo "Invalid employee ID.";
    }
?>
