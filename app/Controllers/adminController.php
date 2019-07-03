<?php 
/**
  * 
  */
 class AdminController
 {
 	private $conf, $OtherModel,$dbCtrl;
 	public function __construct($om,$dbCtrl){
 		$this->OtherModel=$om;
 		$this->dbCtrl=$dbCtrl;
 	}
 	function configuration($confFile){
 	$xmlConf=simplexml_load_string(file_get_contents($confFile));
    $this->conf=$xmlConf;
 	}
 	
 	public function __get($conf) {
    if (property_exists($this, $conf)) {
      return $this->$conf;
    }
  }

    function countXLSX($res,$folder){

    	return sizeof(glob($res."/".$folder."/*"));
    }  
    function checkConfFiles($adr,$fileName,$size,$indexes){
			if(!$this->OtherModel->firstConfXlsx($fileName,$adr,$size,$indexes,$this->dbCtrl))
				return 0;
    		return 1;
    	}

    	function XLSXSFirstData($fileAdr){

    		$excelObj=$this->readXLSX($fileAdr);

			$getSheet=$excelObj->getActiveSheet()->toArray(NULL);
			foreach ($getSheet as $rowID => $row) {
				//Ak je, v tomto prípade 0. prvok, NULL - riadok neexistuje 
				if($row[0]==NULL){$array[$rowID]["empty"]=1;continue;}
				$endDate=$row[2];
				$explodedDate=explode("/", $endDate);
				$array[$rowID]["empty"]=0;
				
				$array[$rowID]["name"]=explode("<br />", nl2br($row[0]));
				$array[$rowID]["email"]=explode("<br />", nl2br($row[1]));
				$array[$rowID]["notif"]=nl2br($row[3]);
				$array[$rowID]["endDate"]=$explodedDate[1].".".$explodedDate[0].".".$explodedDate[2];

				$popis_i=0;
				$array[$rowID]["content"] = nl2br($row[4]);
			}
			return $array;
    	}
    function readXLSX($adr){
        libxml_disable_entity_loader(false);
    	    	// Read the existing excel file
		$inputFileType = PHPExcel_IOFactory::identify($adr);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel=$objReader->load($adr);
		$objPHPExcel->setActiveSheetIndex(0);
		return $objPHPExcel;
    }
    public function delCols($obj,$max,$row,$min="A"){
    	for ($i=$min; $i <= $max; $i++)
			$obj->getActiveSheet()
				->setCellValue($i.$row, NULL);

    }
	public function delXLSXRow($res,$fileName, $iArray){
		$objPHPExcel = $this->readXLSX($res."/xlsxs/".$fileName.".xlsx");
		$topCol=$objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
		foreach ($iArray as $i => $index) {
			$rowID=$this->dbCtrl->getID("dataview","nazov=".$fileName." and rowNO = ".$index);
			// Odstráň najprv riadky v xlsxs
			$this->delCols($objPHPExcel,$topCol,$index,"A");
			// Potom odstráň konfig. súbory
			$this->delete($res."/neov_platby/".$fileName."/".$index);
			// Odstránenie z DB
			$rowID=$this->dbCtrl->getID("dataview","nazov= ".$fileName." and rowNO = ".$index);
			$this->dbCtrl->delete("riadky","ID=".$rowID);
		}
		$this->saveXLSX($objPHPExcel, $res,$fileName);
		return true;
	}
	public function editXLSXRow($res,$fileName, $rows, $iArray){
		$objPHPExcel = $this->readXLSX($res."/xlsxs/".$fileName.".xlsx");
		// Add column headers
		$topCol=$objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
		foreach ($iArray as $i => $index) {
			$okOldArr = $okNewArr = $okSQLArr = $okEmailOldArr = $okEmailNewArr = array();
			$finalName = $finalEmail = "";

			foreach ($rows[$i]["name"] as $nameI => $name) {
				$finalName.=$name;
				$finalEmail.=$rows[$i]["email"][$nameI];

				if ($nameI<(sizeof($rows[$i]["name"])-1)) {
					$finalName.=  "\n";
					$finalEmail.= "\n";

				}
			}
			$objPHPExcel->getActiveSheet()
				->setCellValue('A'.($index), $finalName)
				->setCellValue('B'.($index), $finalEmail)
				->setCellValue('C'.($index), $rows[$i]["endDate"])
				->setCellValue('D'.($index), $rows[$i]["notif"])
				->setCellValue('E'.($index), $rows[$i]["content"]);

			// OdstráŇ zvyšné stĺpce ak je viac položiek
			/*if($topCol>$last_used_col)
				$this->delCols($objPHPExcel,$topCol,$index,$last_used_col);
			*/

			//Najprv potrebujem zistiť ID daného riadku v DB
			$rowID=$this->dbCtrl->getID("dataview","nazov=".$fileName." and rowNO = ".$index);
					
			$prepDate = explode('.', $rows[$i]["endDate"]);
			$finalDate=$prepDate[2]."-".$prepDate[1]."-".$prepDate[0];	
			//a upraviť jej samotné dáta
			$colsArray=array(  
				"poznamka",
				"obsah",
				"expDate"
			);
			$valArray=array(
				$rows[$i]["notif"],
				$rows[$i]["content"],
				$finalDate
			);

			$this->dbCtrl->update("riadky",$colsArray,$valArray,"ID=".$rowID);
			
			// Ťažšia časť je úprava investorov: -> 

			foreach ($rows[$i]["name"] as $nameI => $name) {
				/*************************************
					Pokiaľ nieje daný investor v starom poli, pracuj s ním
					 - array_search vráti iba prvý nájdený index, preto sa nebude v medzitabuľke nachádzať 
					   niektorý investor viackrát
				**************************************/
				$midID=array_search($name, $rows[$i]["nameArr"]);
				$midEmailID=array_search($rows[$i]["email"][$nameI], $rows[$i]["emailArr"]);

				if($midID===false || $midEmailID===false){
					/*************************************
						Pokiaľ nieje daný investor nahraný, pridaj ho do DB a ulož ID
					**************************************/
					if(!$midID=$this->dbCtrl->getID("investori","meno= '".$name."' and email= '".$rows[$i]["email"][$nameI]."'"))
						$midID=$this->dbCtrl->insert("investori","meno, email",array($name, $rows[$i]["email"][$nameI]));	

						array_push($okSQLArr, $midID);	
						array_push($okNewArr, $nameI);
						continue;
					
				}	
				else{
					/*************************************
						Pokiaľ som našiel meno, ulož jeho starý aj nový index
					**************************************/
					if($midID!==false){
						array_push($okOldArr, $midID);
						array_push($okNewArr, $nameI);
					}
					/*************************************
						Pokiaľ som našiel email, ulož jeho starý aj nový index
					**************************************/
					if($midEmailID!==false){
						array_push($okEmailOldArr, $midEmailID);
						array_push($okEmailNewArr, $nameI);
					}

				}
				
			}

				/*************************************
					Napokon spoj oba polia s mailami a menami
				---------------------------------------------
					Nové záznamy
				**************************************/
					$okNewArr += $okEmailNewArr;

				/*************************************
					Staré záznamy
				**************************************/
					$okOldArr += $okEmailOldArr;
			/*************************************
				Update môžeš urobiť iba na pole starých záznamov, preto budeme nimi prechádzať, postupne s nimi pracovať a odstraňovať ich.
			**************************************/
			$oldDiff = array_diff(array_keys($rows[$i]["nameArr"]), $okOldArr);
			$newDiff = array_diff(array_keys($rows[$i]["name"]), $okNewArr);
			$okSQLCount = sizeof($okSQLArr);

				$newDiff = array_values($newDiff);
				$oldDiff = array_values($oldDiff);
			foreach ($oldDiff as $oldIndex => $oldI) {
				/*************************************
					Najskôr spracujeme nových investorov
				**************************************/
				if(!empty($okSQLArr)){
					$invID=$this->dbCtrl->getID("investori","meno= '".$rows[$i]["nameArr"][$oldI]."' and email= '".$rows[$i]["emailArr"][$oldI]."'");
					$invMiddID = $this->dbCtrl->getID("inv_midd","riadok_fk= '".$rowID."' and investor_fk= '".$invID."'");

					$this->dbCtrl->update("inv_midd",array("investor_fk"),array($okSQLArr[$oldIndex]),"ID=".$invMiddID);

					unset($okSQLArr[$oldIndex]);
					continue;
				}

				/*************************************
					Následne už registrovaných
				**************************************/
				if(!empty($newDiff)){
					$newIndex=0;
					echo "newI>>>>   ";var_dump($newIndex); echo"<br>";
					$invID=$this->dbCtrl->getID("investori","meno= '".$rows[$i]["nameArr"][$oldI]."' and email= '".$rows[$i]["emailArr"][$oldI]."'");					
					$invMiddID = $this->dbCtrl->getID("inv_midd","riadok_fk= '".$rowID."' and investor_fk= '".$invID."'");
					$newInvID=$this->dbCtrl->getID("investori","meno= '".$rows[$i]["name"][$newDiff[$newIndex]]."' and email= '".$rows[$i]["email"][$newDiff[$newIndex]]."'");
					
					$this->dbCtrl->update("inv_midd",array("investor_fk"),array($newInvID),"ID=".$invMiddID);
					unset($newDiff[$newIndex]);
					$newIndex++;
					continue;
				}
				/*************************************
					Ak zvýšili staré, odstráň ich
				**************************************/		
					$invID=$this->dbCtrl->getID("investori","meno= '".$rows[$i]["nameArr"][$oldI]."' and email= '".$rows[$i]["emailArr"][$oldI]."'");
					die($invID);
					$this->dbCtrl->delete("inv_midd","riadok_fk=".$rowID." and investor_fk= ".$invID);
			}

				/*************************************
					Ak zvýšili noví investori, pridaj ich
				**************************************/
				foreach ($okSQLArr as $SQLIndex => $SQLI) 
					$this->dbCtrl->insert("inv_midd","riadok_fk, investor_fk",array($rowID, $SQLI));


				/*************************************
					Ak zvýšili novopriradení investori, pridaj ich
				**************************************/
				foreach ($newDiff as $NewIndex => $NewI) {
					$invID=$this->dbCtrl->getID("investori","meno= '".$rows[$i]["name"][$NewI]."' and email= '".$rows[$i]["email"][$NewI]."'");

					$this->dbCtrl->insert("inv_midd","riadok_fk, investor_fk",array($rowID, $invID));
				}
			}				
		$this->saveXLSX($objPHPExcel, $res,$fileName);
		return true;
    }
    public function addXLSXRow($res,$fileName,$newData,$oldSize,$newI){

		$objPHPExcel = $this->readXLSX($res."/xlsxs/".$fileName.".xlsx");
		if(!$this->OtherModel->secondaryConfXlsx($fileName,$res,sizeof($newData)+$oldSize,$newI)) return false;
		// Add column headers
		foreach ($newData as $key => $row) {
			$finalName = $finalEmail = "";
			foreach ($row["name"] as $nameI => $name) {
				$finalName.=$name;
				$finalEmail.=$row["email"][$nameI];

				if ($nameI<sizeof($row["name"])) {
					$finalName.=  "\n";
					$finalEmail.= "\n";

				}
			}

			$objPHPExcel->getActiveSheet()
				->setCellValue('A'.($newI[$key]), $finalName)
				->setCellValue('B'.($newI[$key]), $finalEmail)
				->setCellValue('C'.($newI[$key]), $row["endDate"])
				->setCellValue('D'.($newI[$key]), $row["notif"])
				->setCellValue('E'.($newI[$key]), $row["content"]);
			
			//vlož záznam o riadku
			$fileID=$this->dbCtrl->getID("subory","nazov= ".$fileName);
			//Zisti, či existujú investori a zapamätaj si ich mená
			$rowID=$this->dbCtrl->getID("dataview","nazov=".$fileName." and rowNO = ".$newI[$key]);
			foreach ($row["name"] as $nameI =>$name) {
				if(!$invID=$this->dbCtrl->getID("investori","meno= '".$name."'' and email= '".$row["email"][$nameI]."'"))
					$invID=$this->dbCtrl->insert("investori","meno, email",array($name, $row["email"][$nameI]));
				$this->dbCtrl->insert("inv_midd","riadok_fk, investor_fk",array($rowID, $invID));
			}
			$colsArray=
				"rowNO, 
				subor_fk, 
				poznamka,
				obsah,
				downloaded,
				archived, 
				expirKnow,
				expDate";
			$valArray=array(
				$newI[$key], 
				$fileID,
				$row["notif"],
				$row["content"],
				0,
				0,
				0,
				$row["endDate"]
			);

			/*****************************
                Záznam do DB o riadkoch
        	***************************/
                if(!$fileID=$this->dbCtrl->insert("riadky",$colsArray,$valArray))return false; 

		}
		$this->saveXLSX($objPHPExcel, $res,$fileName);
		return true;
    }
    function saveXLSX($obj, $res,$fileName){
		$objWriter = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
		$objWriter->save($res."/xlsxs/".$fileName.".xlsx");
    }

 	function loadXLSXs($adr, $folderXLSXS, $podm=NULL,$possName=NULL,$searchString = NULL){
 		$xlsxData=array();
			
			/***************************************
				Počiatočné načítanie dát
			***************************************/
			$xlsxsStack = (!empty($possName))?
					((!empty($searchString))?
					$this->dbCtrl->getFullData($possName,$searchString):
					$this->dbCtrl->getFullData($possName)
				):
				((!empty($searchString))?
					$this->dbCtrl->getFullData(NULL,$searchString):
					$this->dbCtrl->getFullData()
				);
			/***************************************
				Prechádzanie každým riadkom
			***************************************/
			foreach ($xlsxsStack as $rowID => $row) {

				$dateDiff=$this->OtherModel->dateDiff($row["enddate"]);

			 	//odstráň súbor (+riadky) a pokračuj, ak neexistuje xlsx súbor)
			 	/*if(empty(glob($adr."/".$folderXLSXS."/".$row["fileName"].".xlsx"))){
			 		$this->dbCtrl->delete("subory","ID=".$row["subor_fk"]);
			 		continue;
			 	}*/ // - blbovzdorné a možno zbytočné 

			 	/******************************************************************************
					Kontrola určitého názvu súboru
				******************************************************************************/
/*				if ($possName!=NULL&&($tempFileAdr!=$possName)) {
					continue;
				}
*/
					/*******************
						Podmienky
					******************/	

				if($podm=="home"||$podm=="init"||$podm=="notif1"||$podm=="notif2")
					if((intval($row["archived"])==1)||($dateDiff<=-90)) continue;
				
				if($podm=="init")
					if (($dateDiff<=-30)||($dateDiff>0)) continue;
				
				if($podm=="notif1")
					if (($dateDiff<=-60)||($dateDiff>-30)) continue;

				if($podm=="notif2")
					if(($dateDiff<=-90)||($dateDiff>-60)) continue;
				if($podm=="book"){
					if(intval($row["archived"])==0) continue;
					$paymentArr=glob($adr."/neov_platby/".$row["nazov"]."/".$row["rowNO"]."/*");
				}
				if($podm=="expired")
					if(($dateDiff>-90)||(intval($row["archived"])==1)) continue;
				if($podm=="expiredCount")
					if(($dateDiff>-90)&&(intval($row["archived"])==0)&&(intval($row["expired"])==1)&&($row["expirKnow"]==0)){
					$count++;
					continue;
				} 
				if($podm=="newFilesCount")
					if($row["downloaded"]==0&&intval($row["archived"])==1){
					$count++;
					continue;
				} 		
				if($row["empty"]==1){	
				$xlsxData[$index][$rowID]["empty"] =1;
				continue;
				}
					/***********************
						Koniec podmienok
					***********************/	
		 		$index=$row["subor_fk"];
				
				$xlsxData[$index][$rowID] =$row;
				/********************************
					Toto prispôsob podľa vlastných potrieb
				********************************/
				foreach ($row["nameArr"] as $key => $value) {
				$xlsxData[$index][$rowID]["name"]     .=$row["nameArr"][$key]."<br>";
				$xlsxData[$index][$rowID]["email"]    .=$row["emailArr"][$key]."<br>";
				}

				$xlsxData[$index][$rowID]["dateDiff"]  =$dateDiff;
				
	 		if ($podm != "noAccFiles")
	 		// Neoverené platby od investora
	 			if (isset($paymentArr)&&sizeof($paymentArr)>1)
	 				$xlsxData[$index][$rowID]["paymentFiles"]=$paymentArr;
			}

			if($podm=="expiredCount"||$podm=="newFilesCount") return $count;
				
				/**************************************
					Nakoniec treba správne zoradiť indexy
				****************************************/
				foreach ($xlsxData as $i => $val) 
				$final[$i] = array_values($val);

 		return array_values($final); 
 	}

 	function deleteAcc($index,$file){
		$doc = new DOMDocument(); 
		$doc->load($file);
		$user = $doc->getElementsByTagName("user")->item($index);
		$user->parentNode->removeChild($user);
		return ($doc->save($file))?0:1;     

 	}
 	function setNewaccData($input,$file, $lmodel, $index=NULL){
 		$email=(isset($input["newAccEmail"]))?$input["newAccEmail"]:$input["email"];
 		$name=(isset($input["newAccEmail"]))?"newAccEmail":"email";

 		$pass=$input["p"];
    
    // Sanitize and validate the data passed in
    $email = filter_input(INPUT_POST, $name, FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Not a valid email
        return 'Emailová adresa nie je v správnej forme.';
    }
 
    $pass = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
    if (strlen($pass) != 128) {
        // The hashed pwd should be 128 characters long.
        // If it's not, something really odd has happened
        return 'Nesprávne konfigúracia hesla.';
    }
    $pass = password_hash($pass, PASSWORD_BCRYPT);

    $doc = new DOMDocument(); 
	$doc->load($file);
    if (isset($input["email"],$input["p"])) {
    	$users = $doc->getElementsByTagName("user");
    	$currAcc=$users->item($index);
	$currAcc->getElementsByTagName("mail")->item(0)->nodeValue = $email;
	$currAcc->getElementsByTagName("pass")->item(0)->nodeValue = $pass;	
    }
    elseif (isset($input["newAccEmail"],$input["p"])) {
    	$root= $doc->getElementsByTagName('doc')->item(0);
    	$user = $doc->createElement('user');
    	$root->appendChild($user);
    	$email = $doc->createElement('mail', $email);
    	$user->appendChild($email);
    	$pass = $doc->createElement('pass', $pass);
    	$user->appendChild($pass);	
    	$lock = $doc->createElement('locked', 0);
    	$user->appendChild($lock);
    	$masta = $doc->createElement('masta', 0);
    	$user->appendChild($masta);
    	$unlockKey = $doc->createElement('unlockKey', $lmodel->createCheckString());
    	$user->appendChild($unlockKey);	
    }
	$doc->save($file);     
       
 			return 0;
 	}

 	function prepareData($data){
 		if(empty($data)) return false;
	  $rowIndex=0;
	  			foreach ($data as $key => $dataVal) {
	  				foreach ($dataVal as $rowID => $row) {
	  					/**********************************
								Ak je prázdny riadok, odstráň ho.
	  					**********************************/
		  				//if ($row["empty"]==1) {unset($data[$key][$rowID]); continue;}

	  					foreach ($row as $colName =>$rowData) $data[$rowIndex][$colName]=$rowData;
		  				$data[$rowIndex]["notif"]=(!empty($row["notif"]))?$row["notif"]:"Žiadna";
		  				if ($row["expir"]==1) {
		  					$data[$rowIndex]["edit"] = '<a href="/expirKnow/" class="'.(($row["expirKnow"]==0)?"active":"").' btn btn-view" ><i class="far fa-eye"></i></a>';	
		  				}else{

		  				$data[$rowIndex]["edit"] = '<button type="button" class="'.(($row["downloaded"]==0&&$row["archived"]==1)?"active":"").' btn btn-view" data-toggle="modal" '.((!empty($row["paymentFiles"]))?('data-target="#investFilesModal" onclick="ajaxInvestFiles('.$key.','.$rowID.','.$data["permitAble"].')" "><i class="fas fa-download"></i>'):('
		  					data-target="#previewMailModal" onclick="ajaxPreview('.$key.','.$rowID.')"><i class="far fa-eye"></i>')).' </button>';
		  				}
		  				$data[$rowIndex]["delete"] = '<form action="/admin/delete" class="m-0" method="POST">
						<input type="hidden" name="name[]" value="'.$dataVal["fileName"].'">
						<input type="hidden" name="area[]" value="'.($rowID+1).'">
						<button class="fs-16 btn btn-delete action" type="button" onclick="delCheck(this)"><i class="clr-red fas fa-times-circle"></i></button></form>';

						$data[$rowIndex]["modal"] = '<button type="button" class="btn btn-view" data-toggle="modal" data-target="#XLSXModal" onclick="viewXLSXModal('.$key.','.$rowID.')"><i class="fas fa-folder-open"></i></button>';
						$rowIndex++;
	  				}
	  				if (empty($data[$key]))
						unset($data[$key]);
	  			}
	  			return (empty($data))?false:json_encode($data);
					
 	}

 	function setConfXML($input,$file){
 		$possibleArray=array("nameX", "nameY", "emailX","emailY","initDateX","initDateY","endDateX","endDateY","productsY","productX","priceX");
 		foreach ($possibleArray as $possibleField) 
 			if (empty($input[$possibleField])) 
 				return $possibleField;
 			
	$doc = new DOMDocument(); 
	$doc->load($file);
	$doc->getElementsByTagName("name")->item(0)->getElementsByTagName("x")->item(0)->nodeValue = intval($input["nameX"]);
	$doc->getElementsByTagName("name")->item(0)->getElementsByTagName("y")->item(0)->nodeValue = intval($input["nameY"]);
	$doc->getElementsByTagName("email")->item(0)->getElementsByTagName("x")->item(0)->nodeValue = intval($input["emailX"]);
	$doc->getElementsByTagName("email")->item(0)->getElementsByTagName("y")->item(0)->nodeValue = intval($input["emailX"]);
	$doc->getElementsByTagName("notif")->item(0)->getElementsByTagName("x")->item(0)->nodeValue = intval($input["notifX"]);
	$doc->getElementsByTagName("notif")->item(0)->getElementsByTagName("y")->item(0)->nodeValue = intval($input["notifY"]);
	$doc->getElementsByTagName("endDate")->item(0)->getElementsByTagName("x")->item(0)->nodeValue = intval($input["endDateX"]);
	$doc->getElementsByTagName("endDate")->item(0)->getElementsByTagName("y")->item(0)->nodeValue = intval($input["endDateY"]);
	$doc->getElementsByTagName("aboutMinRow")->item(0)->nodeValue = intval($input["productsX"]);
	$doc->getElementsByTagName("productX")->item(0)->nodeValue = intval($input["productX"]);
	$doc->getElementsByTagName("priceX")->item(0)->nodeValue = intval($input["priceX"]);
	$doc->save($file);
 	return 0;
}

 	public function delete($path){
		foreach (glob($path."*") as $link){

			if (!is_dir($link)){
				unlink($link);
				return 1;
			} 
			else {
				$this->delete($link."/");
			rmdir($path);	
			}
		}
 	}
}
 	
 