<?php 
  include("db.php"); 
  include("auth.php");

  if(isset($_POST['id'])) {
      $id        = $_POST['id'];
      $deduction = $_POST['deduction'];
      $overtime  = $_POST['overtime'];
      $bonus     = $_POST['bonus'];

      // 1. Kuhaon ang Overtime Rate (emp_id ang bridge)
      $stmtOT = $connection->prepare("SELECT rate FROM overtime WHERE emp_id = ?");
      $stmtOT->bind_param("i", $id);
      $stmtOT->execute();
      $otRate = $stmtOT->get_result()->fetch_assoc()['rate'] ?? 0;

      // 2. Kuhaon ang Salary Rate (emp_id ang bridge)
      $stmtSalary = $connection->prepare("SELECT salary_rate FROM salary WHERE emp_id = ?");
      $stmtSalary->bind_param("i", $id);
      $stmtSalary->execute();
      $salaryRate = $stmtSalary->get_result()->fetch_assoc()['salary_rate'] ?? 0;

      // 3. Calculation
      $overtime_pay = $overtime * $otRate;
      $income = $overtime_pay + $bonus + $salaryRate;
      $netpay = $income - $deduction;

      // 4. I-update ang Employee Table (Gamiton ang bag-ong net_pay column)
      $query = "UPDATE employee SET 
                deduction = ?, 
                overtime = ?, 
                bonus = ?, 
                net_pay = ? 
                WHERE emp_id = ?";

      $stmtUpdate = $connection->prepare($query);
      $stmtUpdate->bind_param("ddddi", $deduction, $overtime, $bonus, $netpay, $id);

      if ($stmtUpdate->execute()) {
        ?>
        <script>
          alert('Success! Net Pay: ₱<?php echo number_format($netpay, 2); ?>');
          window.location.href='home_employee.php';
        </script>
        <?php 
      } else {
        echo "Error: " . $connection->error;
      }
  }
?>