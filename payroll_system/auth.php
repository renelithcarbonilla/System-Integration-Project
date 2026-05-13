<?php
	session_start();
	if(!isset($_SESSION["id"])){ // Nag-check kung naay naka-save nga ID sa session
    	header("Location: login.php");
    	exit(); 
	}
?>