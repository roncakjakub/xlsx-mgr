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
				$array[$rowID]["content"] = nl2br($row[4]);;
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
    public function delCols($obj,$min,$max,$row){
    	for ($i=$min; $i <= $max; $i++) 
			$obj->getActiveSheet()
				->setCellValue($i.$row, NULL);

    }
	public function delXLSXRow($res,$fileName, $iArray){

		$objPHPExcel = $this->readXLSX($res."/xlsxs/".$fileName.".xlsx");
		$topCol=$objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
		foreach ($iArray as $i => $index) {
			// Odstráň najprv riadky v xlsxs
			$this->delCols($objPHPExcel,"A",$topCol,$index);
			// Potom odstráň konfig. súbory
			$this->delete($res."/neov_platby/".$fileName."/".$index);
			// Odstránenie z DB
			$rowID=$this->dbCtrl->getID("dataview","nazov= ".$fileName."and rowNO = ".$index);
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
			$objPHPExcel->getActiveSheet()
				->setCellValue('A'.($index), $rows[$i]["name"])
				->setCellValue('B'.($index), $rows[$i]["email"])
				->setCellValue('C'.($index), $rows[$i]["enddate"])
				->setCellValue('D'.($index), $rows[$i]["notif"])
				->setCellValue('E'.($index), $rows[$i]["popis"]);

			// OdstráŇ zvyšné stĺpce ak je viac položiek
			if($topCol>$last_used_col)
				$this->delCols($objPHPExcel,$last_used_col,$topCol,$index);
			
			//uprav záznam v DB
			$rowID=$this->dbCtrl->getID("dataview","nazov= ".$fileName."and rowNO = ".$index);
			$colsArray=array();
			$valArray=array();
			$this->dbCtrl->update("riadky",$colsArray,$valArray,"ID=".$rowID);
		}
			//uprav záznam investorov
		

					
		$this->saveXLSX($objPHPExcel, $res,$fileName);
		return true;
    }
    public function addXLSXRow($res,$fileName,$newData,$oldSize,$newI){

		$objPHPExcel = $this->readXLSX($res."/xlsxs/".$fileName.".xlsx");
		if(!$this->OtherModel->firstConfXlsx($fileName,$res,sizeof($newData)+$oldSize,$newI,$this->dbCtrl)) return false;
		// Add column headers
		foreach ($newData as $key => $row) {
			$objPHPExcel->getActiveSheet()
				->setCellValue('A'.($newI[$key]), $row["name"])
				->setCellValue('B'.($newI[$key]), $row["email"])
				->setCellValue('C'.($newI[$key]), $row["enddate"])
				->setCellValue('D'.($newI[$key]), $row["notif"])
				->setCellValue('E'.($newI[$key]), $row["popis"]);
			
			//vlož záznam o riadku
			$fileID=$this->dbCtrl->getID("subory","nazov= ".$fileName);
			$colsArray=array(
				"rowNO", 
				"subor_fk", 
				"poznamka",
				"obsah",
				"downloaded",
				"archived", 
				"expirKnow",
				"expDate"
			);
			$valArray=array(
				$newI[$key], 
				$fileID,
				$row["notif"],
				$row["popis"],
				0,
				0,
				0,
				$row["enddate"]
			);
			//Zisti, či existujú investori a zapamätaj si ich mená
			$rowID=$this->dbCtrl->getID("dataview","nazov= ".$fileName."and rowNO = ".$newI[$key]);
			foreach ($row["name"] as $name) {
				if(!$invID=$this->dbCtrl->getID("investori","meno= ".$row["name"]."and email= ".$row["email"]))
					$invID=$this->dbCtrl->insert("investori",array("meno", "email"),array($row["name"], $row["email"]));
				$this->dbCtrl->insert("inv_midd",array("riadok_fk", "investor_fk"),array($rowID, $invID));
			}
		}
		$this->saveXLSX($objPHPExcel, $res,$fileName);
		return true;
    }
    function saveXLSX($obj, $res,$fileName){
		$objWriter = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
		$objWriter->save($res."/xlsxs/".$fileName.".xlsx");
    }

 	function loadXLSXs($adr, $folderXLSXS,$folderPays, $podm=NULL,$possName=NULL){
 		$xlsxData=$possIndexes=array();
 		$index=0;
	 	$fileArr=glob($adr."/".$folderXLSXS."/*.xlsx");
		foreach ($fileArr as $fileAdr) {
			/***************************************
				Prechádzanie každým súborom
			***************************************/
			$tempFileAdr=explode(".xlsx",basename($fileAdr))[0];
			/******************************************************************************
				Kontrola určitého názvu súboru ( aby nemusel prehľadávať všetky súbory )
			******************************************************************************/
			if ($possName!=NULL&&($tempFileAdr!=$possName)) {
				continue;
			}
			/***************************************
				Počiatočná kontrola konf. súborov
			***************************************/
			
			$xlsxsStack = $this->dbCtrl->getFullData($tempFileAdr);
			/***************************************
				Prechádzanie každým riadkom
			***************************************/
			
			foreach ($xlsxsStack as $rowID => $row) {
				if($row["empty"]==0)
					array_push($possIndexes, $rowID+1);
				else{	
					$xlsxData[$index]["fileData"][$rowID]["empty"]=1;
					continue;
				}
				
				$dateDiff=$this->OtherModel->dateDiff($row["endDate"]);
				$areaConfFile=simplexml_load_string(file_get_contents($adr."/".$folderPays."/".$tempFileAdr."/".($rowID+1)."/downloadedFiles.xml"));
				$paymentArr=glob($adr."/".$folderPays."/".$tempFileAdr."/".($rowID+1)."/*");
					/*******************
						Podmienky
					******************/	
			if ($podm=="init") {
			}
				if ($areaConfFile->deleted==1) continue;
				if($folderXLSXS=="xlsxs")
					if(($dateDiff<=-90)&&($areaConfFile->archived==0))
						if($areaConfFile->expired==0)
							$this->OtherModel->createUploadXML(0, $adr."/neov_platby/".$tempFileAdr."/".($rowID+1)."/downloadedFiles.xml","expired",1);
				if($podm=="home"||$podm=="init"||$podm=="notif1"||$podm=="notif2")
					if(($areaConfFile->archived==1)||($dateDiff<=-90)||sizeof($paymentArr)>1) continue;
				
				if($podm=="init")
					if (($dateDiff<=-30)||($dateDiff>0)) continue;
				
				if($podm=="notif1")
					if (($dateDiff<=-60)||($dateDiff>-30)) continue;

				if($podm=="notif2")
					if(($dateDiff<=-90)||($dateDiff>-60)) continue;
				if($podm=="book")
					if((sizeof($paymentArr)==1)||($areaConfFile->archived==0)) continue;
				if($podm=="expired")
					if(($dateDiff>-90)||($areaConfFile->archived==1)) continue;
				if($podm=="expiredCount")
					if(($dateDiff>-90)&&($areaConfFile->archived==0)&&($areaConfFile->expired==1)&&($areaConfFile->expirKnow==0)){
					$count++;
					continue;
				} 
				if($podm=="newFilesCount")
					if($areaConfFile->downloaded==0&&$areaConfFile->archived==1){
					$count++;
					continue;
				} 		
				if($row["empty"]==1){	
				$xlsxData[$index]["fileData"][$rowID]["empty"] =1;
				continue;
				}
					/***********************
						Koniec podmienok
					***********************/	
		 		
			/*************************************
				Kontrola a vytvorenie priečinkovej štruktúry + DB
			*************************************/
			/*if($folderXLSXS=="xlsxs")
				if(!$this->OtherModel->firstConfXlsx($tempFileAdr,$adr,sizeof($xlsxsStack),$possIndexes,$this->dbCtrl)) return false;
 			*/	

				$xlsxData[$index]["fileAdr"] =$fileAdr;
				$xlsxData[$index]["fileName"] =$tempFileAdr;
				$xlsxData[$index]["fileData"][$rowID]["downloaded"] = ($areaConfFile->downloaded==1)?1:0;
				$xlsxData[$index]["fileData"][$rowID]["deleted"] = ($areaConfFile->deleted==1)?1:0;
				$xlsxData[$index]["fileData"][$rowID]["archived"] = ($areaConfFile->archived==1)?1:0;
				$xlsxData[$index]["fileData"][$rowID]["expirKnow"] = ($areaConfFile->expirKnow==1)?1:0;
				$xlsxData[$index]["fileData"][$rowID]["expir"] = ($areaConfFile->expired==1)?1:0;
				/********************************
					Toto prispôsob podľa vlastných potrieb
				********************************/
				foreach ($row["name"] as $key => $value) {
				$xlsxData[$index]["fileData"][$rowID]["name"]     .=$row["name"][$key]."<br>";
				$xlsxData[$index]["fileData"][$rowID]["email"]    .=$row["email"][$key]."<br>";
				}
				$xlsxData[$index]["fileData"][$rowID]["notif"]    =$row["notif"];
				$xlsxData[$index]["fileData"][$rowID]["enddate"]  =date("d.m.Y",strtotime($row["endDate"]));
				$xlsxData[$index]["fileData"][$rowID]["dateDiff"]  =$dateDiff;
				
	 			$xlsxData[$index]["fileData"][$rowID]["popis"]=$row["popis"];
	 		if ($podm != "noAccFiles")
	 		// Neoverené platby od investora
	 			if (sizeof($paymentArr)>1)
	 				$xlsxData[$index]["fileData"][$rowID]["paymentFiles"]=$paymentArr;
			}
			$index++;

 		}
			if($podm=="expiredCount"||$podm=="newFilesCount") return $count;
 		return $xlsxData; 
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
	  						
	  				foreach ($dataVal["fileData"] as $rowID => $row) {
	  					/**********************************
								Ak je prázdny riadok, odstráň ho.
	  					**********************************/
		  				if ($row["empty"]==1) {unset($data[$key]["fileData"][$rowID]); continue;}

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
	  				if (empty($data[$key]["fileData"]))
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
 	
 