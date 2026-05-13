<?php
    require("db.php"); // Logic: Calculate Net Pay with deductions
    
    // Get POST data safely
    $id = $_POST['salary_id'] ?? null;
    $salary = $_POST['salary_rate'] ?? null;

    if ($id && $salary) {
        // Using prepared statements to prevent SQL injection
        $sql = $connection->prepare("UPDATE salary SET salary_rate = ? WHERE salary_id = ?");
        $sql->bind_param("di", $salary, $id); 
        
        if ($sql->execute()) {
            ?>
            <script>
                alert('Salary rate has been updated!');
                window.location.href = 'home_salary.php';
            </script>
            <?php 
        } else {
            echo "Something went wrong, please try again!";
        }
        
        $sql->close();
    } else {
        echo "Invalid input!";
    }

    // Close connection
    $connection->close();
?>
