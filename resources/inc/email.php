<?php 
$prijemca = "jakub.roncak@gmail.com";
$name=$_POST["name"];
$email=$_POST["email"];
$text=$_POST["text"];

if ($name && $email && $text){


if($mail=mail($prijemca, "Správa z webstránky", str_replace("\n.", "\n..", $text),"From: ".$email))
	header($_SERVER["HTTP_REFERER"]);
else echo $mail;
}
else echo "chyba2";
?>