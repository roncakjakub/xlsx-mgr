<?php 
if (isset($_POST["nazov"],$_POST["oslovenie"],$_POST["content"])) {
	$message="";
	$nadpis = "1. upozornenie";
	$oslovenie= $_POST["oslovenie"];
	$content = $_POST["content"];

	include_once '../../resources/mail/_top.php'; 
	include_once '../../resources/mail/first.php'; 
	include_once '../../resources/mail/_bottom.php'; 
	echo $message;
	}
?>