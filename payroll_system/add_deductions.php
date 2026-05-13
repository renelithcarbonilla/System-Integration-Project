<?php
	
		require("db.php");
		
		@$id 			= $_POST['deduction_id'];
		@$healthinsurance 	= $_POST['healthinsurance'];
		@$garnishments 			= $_POST['garnishments'];
		@$others 			= $_POST['others'];
		@$fica 			= $_POST['fica'];
		@$loans 		= $_POST['loans'];

		$stmt = $connection->prepare("UPDATE deductions SET garnishments=?, others=?, fica=?, loans=?, healthinsurance=? WHERE deduction_id=1");
		$stmt->bind_param("sssss", $garnishments, $others, $fica, $loans, $healthinsurance);
		if ($stmt->execute()) {
    		echo "<script>alert('Deductions updated!'); window.location.href='home_deductions.php';</script>";
		} 
		else {
    		echo "Something went wrong, Please try again!";
		}
 ?>