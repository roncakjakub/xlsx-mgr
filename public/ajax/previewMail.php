<?php 
if (isset($_POST["nazov"],$_POST["oslovenie"],$_POST["popis"],$_POST["cena"])) {
	$message="";
	$nadpis = "1. upozornenie";
	$oslovenie= $_POST["oslovenie"];
	$popis = $_POST["popis"];
	$cena = $_POST["cena"];

	include_once '../../resources/mail/_top.php'; 
	include_once '../../resources/mail/first.php'; 
	include_once '../../resources/mail/_bottom.php'; 
	echo $message;
	}
?>