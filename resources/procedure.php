<?php
$fileArr=glob($this->resources.'/xlsxs/*.xlsx');

$myMail=$this->LoginController->getEmails()[0];
$this->AdminController->configuration($this->resources.'/secXML/xlsxConf.xml');

$xlsxStack=$this->AdminController->loadXLSXs($this->resources, "xlsxs", "neov_platby");
foreach ($xlsxStack as $xlsxData) {
	$modifyTime=filectime($xlsxData["fileAdr"]);
	$fileName=$xlsxData["fileName"];
	foreach ($xlsxData["fileData"] as $rowID => $row) {
		if ($row["deleted"]==1||$row["archived"]==1||$row["expired"]==1) continue;
	$oslovenie="Dobrý deň p. ".$row["name"].",";
	$email=$row["email"];
	$endDatediff=$this->OtherModel->dateDiff($row["enddate"]);//skontroluj dátumy
		$message="";
		/***********************************
		Pokiaľ je do skončenia faktúry 1 mesiac alebo menej, odošli 1. email
	**********************************/
	if ($endDatediff==30) {
		$popis = $row["popis"];
		$cena = $row["cena"];
		/*********************************************
			Priradenie kľúčov (názvov) a hodnôt (cien) do poľa popisov
		*********************************************/

		$link=$this->OtherModel->toASCII($fileName)."&area=".($rowID+1)."&email=".$email;
		$subject = "Vaša objednávka bola inicializovaná";
		$nadpis = "Inicializácia objednávky";
		include $this->resources.'/mail/_top.php';
		include $this->resources.'/mail/first.php';
		include $this->resources.'/mail/_bottom.php';
		mail($email,$subject,$message,$headers);
	}else{
		/***********************************
			Pokiaľ je v druhom dátume, odošli 2. email o expirácií
		**********************************/
		if ($endDatediff==0) {
			$ownText = "Dnešným dňom Vám expiruje faktúra. Prosíme o skoré odoslanie potvrdenia platby. Ďakujeme.";
				$nadpis = $subject= "1. Upozornenie";
		}else
		/***********************************
			1 mesiac po expirácií
		**********************************/
		if ($endDatediff==-30) {
			$ownText = "2. upozornenie o neúhrade faktúry. Prosíme o skoré odoslanie potvrdenia platby. Ďakujeme.";
			$nadpis = $subject= "2. Upozornenie";
		}else
		/***********************************
			2 mesiace po expirácií
		**********************************/
		if ($endDatediff==-60) {
			$ownText = "3. upozornenie o neúhrade faktúry. Prosíme o skoré odoslanie potvrdenia platby. Ďakujeme.";
			$nadpis = $subject= "3. Upozornenie";
		}else
		/***********************************
			3 mesiac po expirácií
		**********************************/
		if ($endDatediff==-90) {
			$message="";
			$subject = "Expirovaná faktúra";
			$ownText = "Platnosť faktúry na meno ".$row["name"]." dnes skončila.";
			$nadpis = "Expirovaná faktúra";
			$oslovenie="Vážený administrátor,";
			$myMail = "probim@probim.sk";
			include $this->resources.'/mail/_top.php';
			include $this->resources.'/mail/expirMail.php';
			include $this->resources.'/mail/_bottom.php';
			$allEmails = $this->LoginController->getEmails();
			foreach ($allEmails as $email) 
			mail($email,$subject,$message,$headers);
		
		/************************************************
			Kontrola oblastí pre prípadný presun súboru
		************************************************/
			
			$ownText = "Dnešným dňom Vám ubehli 3 mesiace expiračnej lehoty k zaplateniu faktúry. Do niekoľkých dní Vás bude kontaktovať administrátor.";
			$nadpis = $subject= "3. Upozornenie";
			//$this->OtherModel->lastsFileCheck($fileName,$this->resources);
			$this->OtherModel->createUploadXML(0, $this->resources."/neov_platby/".$fileName."/".($rowID+1)."/downloadedFiles.xml","expired",1);
		}
		else continue;

		include $this->resources.'/mail/_top.php';
		include $this->resources.'/mail/expirMail.php';
		include $this->resources.'/mail/_bottom.php';
		mail($email,$subject,$message,$headers);
		}
	}
}
?>
