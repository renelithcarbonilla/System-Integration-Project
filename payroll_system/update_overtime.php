<?php
    require("db.php");
    
    // Get POST data safely
    $id = $_POST['ot_id'] ?? null;
    $overtime = $_POST['rate'] ?? null;

    if ($id && $overtime) {
        // Using prepared statements to prevent SQL injection
        $sql = $connection->prepare("UPDATE overtime SET rate = ? WHERE ot_id = ?");
        $sql->bind_param("di", $overtime, $id); 
        
        if ($sql->execute()) {
            ?>
            <script>
                alert('Overtime Rate has been updated!');
                window.location.href = 'home_salary.php';
            </script>
            <?php 
        } else {
            echo "Something went wrong!";
        }
        
        $sql->close();
    } else {
        echo "Invalid input!";
    }

    // Close connection
    $connection->close();
?>
