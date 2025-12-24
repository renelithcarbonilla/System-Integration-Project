<?php
	require("db.php");
	@$id = $_POST['ot_id'];
	@$overtime = $_POST['rate'];

	$stmt = $connection->prepare("UPDATE overtime SET rate = ? WHERE ot_id = 1");
	$stmt->bind_param("s", $overtime);

	if ($stmt->execute()) {
		?>
    		<script>
				alert('Overtime rate per hour successfully changed...'); 
				window.location.href='home_salary.php';
			</script>
		<?php
	} else {
     "Not Successful!";
	}
 ?>