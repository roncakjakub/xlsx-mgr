<?php
$crypt=$text=""; 
foreach (unpack("C*", "nJaneeek") as $value) {
	$crypt.=($value-55);
};
	echo ($crypt." ");	
foreach (str_split($crypt, 2) as $char) {
	$text.=chr($char+55);
}
var_dump($text);
	if (isset($_POST["mail"],$_POST["text"])) {
 	if(mail($_POST["mail"], "Skúška stránky", $_POST["text"],"From:jakub.roncak@gmail.com"))
 		echo "Oka";
 	else echo "Probľem";
 } ?>
<!DOCTYPE html>
<html>
<head>
	<title>Mailer</title>
</head>
<body>
<form method="POST">
	<input type="text" name="mail" value="@">
	<textarea name="text" placeholder="Zadaj text"></textarea>
	<button type="submit">Odošli</button>
</form>
</body>
</html>