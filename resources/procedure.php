<?php
$fileArr=glob($this->resources.'/xlsxs/*.xlsx');

$myMail=$this->LoginController->getEmails()[0];
$this->AdminController->configuration($this->resources.'/secXML/xlsxConf.xml');

	/*****************************
		Vloženie dát nových záznamov do DB + priečinková štruktúra
	*****************************/

	foreach ($fileArr as $key => $file) {
		$fileName=explode(".", basename($file))[0];
		if(!glob($this->resources.'/neov_platby/'.$fileName)){	
			$newXlsxData=$this->AdminController->XLSXSFirstData($file);
			
		
			$rowStack=array();
			foreach ($newXlsxData as $rowI => $rowData)
				if ($rowData["empty"]!=1) array_push($rowStack, ($rowI+1));
					
			$fileID=$this->OtherModel->primaryConfXlsx($fileName,$this->resources,$this->DbController,$rowStack);
				
			foreach ($newXlsxData as $rowCounter => $rowData){
				if ($rowData["empty"]==1) continue;
			$colsArray="rowNO,subor_fk,poznamka,obsah,downloaded,archived, expirKnow,expDate";

			/*****************************
				Prevod dátumu do správneho formátu
			*****************************/
			$workDate=explode(".", $rowData["endDate"]);
			$finalDate = $workDate[2]."-".$workDate[1]."-".$workDate[0];
			$valArray=array(
				($rowCounter+1), 
				$fileID,
				$rowData["notif"],
				$rowData["content"],
				0,
				0,
				0,
				$finalDate
				//skontroluj
			);
			/*****************************
                Záznam do DB o riadkoch
        	***************************/
            $rowID=$this->DbController->insert("riadky",$colsArray,$valArray);
			foreach ($rowData["name"] as $nameI => $name) {
				/*************************************
					Pokiaľ nieje daný investor nahraný pridaj ho do DB
				**************************************/
				if(!$invID=$this->DbController->getID("investori","meno= '".$name."' and email= '".$rowData["email"][$nameI]."'"))
					$invID=$this->DbController->insert("investori","meno, email",array($name, $rowData["email"][$nameI]));
				/*************************************
					Pridaj ho ku záznamu, ak tam už nie je
				**************************************/
				if(!$this->DbController->getID("inv_midd","riadok_fk= '".$rowID."' and investor_fk= '".$invID."'"))
				$this->DbController->insert("inv_midd","riadok_fk, investor_fk",array($rowID, $invID));
			}
		}
	}
}
	/******************************
		Čítanie z DB
	*****************************/
die();
$xlsxStack=$this->AdminController->loadXLSXs($this->resources, "xlsxs");
foreach ($xlsxStack as $xlsxData) {
	foreach ($xlsxData as $rowID => $row) {
		if ($row["deleted"]==1||$row["archived"]==1||$row["expired"]==1) continue;
	
	$endDatediff=$this->OtherModel->dateDiff($row["enddate"]);//skontroluj dátumy
	$headers = EMAIL_HEADERS. 'From: <'.$myMail.'>' . "\r\n";

		/***********************************
			Pokiaľ je do skončenia faktúry 1 mesiac odošli 1. email
		**********************************/
	if (/*$endDatediff==30||*/1) {

		$emailData["nadpis"] = "Inicializácia objednávky";
		$message=$this->OtherModel->getmailData($this->resources,"first",$row,$emailData);
		$subject = "Vaša objednávka bola inicializovaná";
		
		/*************************************************
			Odošle email všetkým investorom daného záznamu
		**************************************************/

		foreach ($row["emailArr"] as $emailIndex => $email){	
			$emailData["link"]=$this->OtherModel->toASCII($fileName)."&area=".($rowID+1)."&email=".$email;
			var_dump($emailData["link"]);die;
			
			mail($email,$subject,$message[$emailIndex],$headers);
		}
	
	die();
	
	}else{
		/***********************************
			Pokiaľ je v druhom dátume, odošli 2. email o expirácií
		**********************************/
		if ($endDatediff==0) {
			$emailData["content"] = "Dnešným dňom Vám expiruje faktúra. Prosíme o skoré odoslanie potvrdenia platby. Ďakujeme.";
			$emailData["nadpis"] = $subject= "1. Upozornenie";
		}else
		/***********************************
			1 mesiac po expirácií
		**********************************/
		if ($endDatediff==-30) {
			$emailData["content"] = "2. upozornenie o neúhrade faktúry. Prosíme o skoré odoslanie potvrdenia platby. Ďakujeme.";
			$emailData["nadpis"] = $subject= "2. Upozornenie";
		}else
		/***********************************
			2 mesiace po expirácií
		**********************************/
		if ($endDatediff==-60) {
			$emailData["content"] = "3. upozornenie o neúhrade faktúry. Prosíme o skoré odoslanie potvrdenia platby. Ďakujeme.";
			$emailData["nadpis"] = $subject= "3. Upozornenie";
		}else
		/***********************************
			3 mesiac po expirácií
		**********************************/
		if ($endDatediff==-90) {
			$emailData["content"] = "Platnosť faktúry na meno ".$row["name"]." dnes skončila.";
			$emailData["nadpis"] = $subject = "Expirovaná faktúra";
			$emailData["oslovenie"]="Vážený administrátor,";
			$message=$this->OtherModel->getmailData($this->resources,"expirMail",$row,$emailData);
			$headers = EMAIL_HEADERS. 'From: <"probim@probim.sk">' . "\r\n";
		
			$allEmails = $this->LoginController->getEmails();
			foreach ($allEmails as $email) 
			mail($email,$subject,$message,$headers);		
			
			$emailData["content"] = "Dnešným dňom Vám ubehli 3 mesiace expiračnej lehoty k zaplateniu faktúry. Do niekoľkých dní Vás bude kontaktovať administrátor.";
			$emailData["nadpis"] = $subject= "3. Upozornenie";
			//$this->OtherModel->lastsFileCheck($fileName,$this->resources);
    		$rowID=$this->DbController->getID("dataview","nazov=".$nazov."and rowNO = ".$area);
    		$this->DbController->update("riadky",array("expired"),array(1),"ID=".$rowID);
			
		}
		else continue;

		/*************************************************
			Odošle email všetkým investorom daného záznamu
		**************************************************/

		$message=$this->OtherModel->getmailData($this->resources,"expirMail",$row,$emailData);
		foreach ($row["emailArr"] as $emailIndex => $email)
			mail($email,$subject,$message[$emailIndex],$headers);
		}
	}
}
?>
