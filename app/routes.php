<?php
$data= array();
$sessSettings = [
  'name' => 'dummy_session',
  'lifetime' => '1 hour'
];
				/********************************
						Cron
				********************************/
$app->get('/HEXaCRON', function ($request, $response,$args) use ($data) {
	$data['baseurl'] = $request->getUri()->getBaseUrl();
	// otvorenie súboru cronu
	require_once ($this->resources.'/procedure.php');
	return 1;
})->add(new \Slim\Middleware\Session($sessSettings));

//dokonči bezpečnosť
/*$app->get('/public/ajax[/{nieco}]', function ($request, $response,$args) use ($data) {
		$data['baseurl'] = $request->getUri()->getBaseUrl();
		return $response->withRedirect($data['baseurl']);	
});*/
				/********************************
					Odblokovanie účtu
				********************************/

$app->get('/unlock/{hash}&id={id}', function ($request, $response,$args) use ($data) {

		$data['baseurl'] = $request->getUri()->getBaseUrl();
		//Ak je hash v URL rovnaký ako ten v xml súbore, poď ďalej
		$this->LoginController->setAccData($args["id"]);
		if($args["hash"]==$this->LoginController->unlockKey)
			{

				//prestav hodnoty a vlož nový bezpečnostný kód
			if($this->OtherModel->accountLocking($this->resources."/secXML/users.xml",0,$id,$this->LoginModel->createCheckString()))
				$this->session->set('alert', 1);
			else $this->session->set('alert', 0);
		}
		else $this->session->set('alert', 0);
		return $response->withRedirect($data['baseurl']);	
})->add(new \Slim\Middleware\Session($sessSettings));


$app->get('/expirKnow/{file}&area={area}', function ($request, $response,$args) use ($data) {

	$rowID=$this->DbController->getID("dataview","nazov=".$args["file"]."and rowNO = ".$args["area"]);

    if($this->DbController->update("riadky",array("expirKnow"),array(1),"ID=".$rowID))
		$this->session->set('alert', 1);
	else $this->session->set('alert', 0);
	return true;	
})->add(new \Slim\Middleware\Session($sessSettings));
	

				/********************************
					Nahratie súborov investora
				********************************/

$app->get('/upload/{ascii}&area={area}&email={email}', function ($request, $response,$args) use ($data) {
	if (isset($this->session->alert)) include $this->resources.'/alert.php';

	/********************
		Nastavenie
	********************/
	
	$data['baseurl'] = $request->getUri()->getBaseUrl();
	$this->UploadController->init($args["ascii"], $this->resources);
    $this->AdminController->configuration($this->resources.'/secXML/xlsxConf.xml');
	$xlsxStack=$this->AdminController->loadXLSXs($this->resources, "xlsxs" );


	//Zrýchliť by to bolo možné vytiahnutím iba tých dát z DB, ktoré sú relevantné


	/*************************************
		Skontroluje, či sedú názov z GET-u s názvom súboru
	*************************************/
	if(!$data["fileName"]=$this->UploadController->nameCheck()){
		$this->session->set('alert', 0);
		return $response->withRedirect($data['baseurl']);	
	}else{
		$data["fileName"]=explode(".", basename($data["fileName"]))[0];
		foreach ($xlsxStack as $xlsxData)
			foreach ($xlsxData[$args["area"]]["emailArr"] as $key => $email) 	
					if ($email == $args["email"])
						return $this->OtherModel->drawBasicPage($this->view,$response,$data,'views/upload/index.phtml');

	/*************************************
		Ak niečo nesedí, vráť ERROR SESSION.
	*************************************/
				
		$this->session->set('alert', 0);
		return $response->withRedirect($data['baseurl']);
	}
})->add(new \Slim\Middleware\Session($sessSettings));

$app->post('/upload/{ascii}&area={area}&email={email}', function ($request, $response,$args) use ($data) {

	/********************
		Nastavenie
	********************/

	$data['baseurl'] = $request->getUri()->getBaseUrl();
	$directory = "../resources/neov_platby";
	$post=$request->getParsedBody();
	$uploadedFiles = $request->getUploadedFiles();
    $uploadedFile = $uploadedFiles['checkPaymentFile'];
	
	/*****************************
		Samotné nahranie súborov
	*****************************/
	$possFormats=array("jpeg","jpg","pdf","tiff","bmp","png");
	$uplStatus=$this->UploadController->uploadFiles($directory,$uploadedFiles['checkPaymentFile'], $possFormats,0,$post["fileName"],$args["area"]);
    if($uplStatus===1){
    	$this->AdminController->configuration($this->resources.'/secXML/xlsxConf.xml');
		$xlsxStack=$this->AdminController->loadXLSXs($this->resources, "xlsxs" );   

	/*****************************
		Zistenie mena z xlsxs
	*****************************/
    	foreach ($xlsxStack as $xlsxData) 
    	if (OtherModel::fromASCII($args["ascii"])==$xlsxData["fileName"]) {
		$uploaderName=$xlsxData[$args["area"]-1]["name"];
		break;
    	}
    /*****************************
		Poslanie informačného mailu adminovi 
	*****************************/
    	$nadpis = "Boli nahrané súbory";
    	$oslovenie="Vážený adminstrátor,";
		$ownText="Na stránke sme zaregistrovali nahranie súborov na meno ".$uploaderName." .";
		$myMail="admin@probim.sk";
		include_once $this->resources.'/mail/_top.php';
		include_once $this->resources.'/mail/alertMail.php';
		include_once $this->resources.'/mail/_bottom.php';
		$allEmails = $this->LoginController->getEmails();
		foreach ($allEmails as $email) 
		mail($email, "Boli nahrané súbory",$message ,$headers);
    	$this->session->set('alert', 1);
    	return $response->withRedirect("./".$args["ascii"]."&area=".$args["area"]."&email=".$args["email"]."");
    }
    else {
    	$uplStatus="Pri nahrávaní sa nepodarilo preniesť niektoré súbory: <strong>".$uplStatus."</strong>. <br><br> Skúste skontrolovať formáty súborov.";
    	$this->session->set('alert', $uplStatus);

    	return $response->withRedirect("./".$args["ascii"]."&area=".$args["area"]."&email=".$args["email"]."");
    	}
    
})->add(new \Slim\Middleware\Session($sessSettings));

	/********************
		Vyhľadávanie
	********************/

$app->post('/search', function ($request, $response,$args) use ($data) {

	/********************
		Nastavenie
	********************/
	$data['baseurl'] = $request->getUri()->getBaseUrl();
	$directory = "../resources/neov_platby";
	$post=$request->getParsedBody();
	$xlsxData=$this->AdminController->loadXLSXs($this->resources,"xlsxs",NULL,NULL,$post["string"]);
	$data["preparedData"]=$this->AdminController->prepareData($xlsxData);
		//ajaxom pošli dáta 
    	return $response;
    	
    
})->add(new \Slim\Middleware\Session($sessSettings));
				/********************************
					Adminstrátor
				********************************/

$app->post('/admin[/{section}[/{adds}]]', function ($request, $response,$args) use ($data) {
	/*****************************
		Nastavenie
	*****************************/
	if (isset($this->session->alert)) include $this->resources.'/alert.php';
	$data['baseurl'] = $request->getUri()->getBaseUrl();
	$this->AdminController->configuration($this->resources. '/secXML/xlsxConf.xml');
	/*****************************
		Kontrola prihlásenia
	*****************************/
	if (!$this->LoginModel->login_check($this->LoginController,$this->session,$this->LoginController->settedPass,$this->LoginController->locked)) 
		return $response->withRedirect($data['baseurl']);
	
	/*****************************
		Samotné sekcie
	*****************************/
	if (isset($args["section"])) {
		$section=$args["section"];
    $post=$request->getParsedBody();
    	if ($section=="settings") {
				if ($args["adds"]=="deleteAccount"&&!empty($post["accID"])) {
					$post["accID"]=$post["accID"][0];
					$delAccErr=$this->AdminController->deleteAcc($post["accID"], $this->resources. '/secXML/users.xml');
					if($delAccErr===0){
						/************************************
			Pokiaľ je index uživateľa, ktorého odstraňujem menší, ako môj, zníž môj index o 1
						**************************************/	
						if ($post["accID"]<$this->session->accI) 
								$this->session->set("accI",$this->session->accI-1);
								
						$this->session->set('alert', 1);
						return $response->withRedirect("./mastaAccs");
					}
					else{
						$this->session->set('alert', 0);
						return $response->withStatus(500)->withRedirect("./mastaAccs");
					}
				}
				elseif (isset($post["email"],$post["p"])) {
					$newAccDataErr=$this->AdminController->setNewaccData($post, $this->resources. '/secXML/users.xml', $this->LoginModel,$this->session->accI);
					if($newAccDataErr===0){	
						$this->session->set('alert', 1);
						$this->LoginController->logOut();
						return $response->withRedirect($data['baseurl']);
					}
					else{
						$this->session->set('alert', 0);
						return $response->withStatus(500)
							->withRedirect($data['baseurl'])
            				->withHeader('Content-Type', 'text/html')
            				->write('Názov chyby: '.$newAccDataErr.'. <br> Obrátte sa s tým na administrátora.');
					}
				}
				elseif (isset($post["newAccEmail"],$post["p"])) {
					$newAccDataErr=$this->AdminController->setNewaccData($post, $this->resources. '/secXML/users.xml', $this->LoginModel);
					if($newAccDataErr==0){	
						$this->session->set('alert', 1);
						return $response->withRedirect($data['baseurl']);
					}
					else{
						$this->session->set('alert', 0);
					return $response->withRedirect($data['baseurl']);
					}
				}

				if ($args["adds"]=="xy") {
					if(!$this->AdminController->setConfXML($post, $this->resources. '/secXML/xlsxConf.xml'))
						$this->session->set('alert', 1);
					else {
						$this->session->set('alert', 0);
						return $response->withRedirect($data['baseurl']);
					}
				}
    		}
		if ($section=="delete") {
			/*******************
				4 riadky nadol skontroluj a uprav cesty - area
			
				Odstraňovanie bude prebiehať iba vo virtuálnej hladine ( DB ). Pre daný riadok sa nastaví deleted = 1 a bude ho možné zobraziť ???...bohviekde..??? ( asi archív )

			*******************/
			$fileName=$post["name"][0];
			$rowIndex=$post["area"][0];

			$rowID=$this->DbController->getID("dataview","nazov = ".$fileName." and rowNO = ".$rowIndex);

		    if($this->DbController->update("riadky",array("archived", "downloaded"),array(1,1),"ID=".$rowID))
				$this->session->set('alert', 1);
			else $this->session->set('alert', 0);
			}

		if ($section=="totaldelete"){
			/*******************
			
				Odstraňovanie bude prebiehať aj fyzicky v súbore a v DB.

			*******************/
			$fileName=$post["name"][0];
			$rowArr=$post["area"];
			$payAdress=$this->resources."/neov_platby/".$xlsxID."/".$rowID;
			$this->AdminController->delXLSXRow($this->resources,$fileName, $rowArr);
				$this->session->set('alert', 1);

		}
		if ($section=="update") {
			/**************************************************
				Nahranie súborov do dočasného uložiska
			**************************************************/
			$data['baseurl'] = $request->getUri()->getBaseUrl();
			$directory = "../resources/tempXLSXS";
			$post=$request->getParsedBody();
			$uploadedFilesStack = $request->getUploadedFiles();
		    $uploadedFiles = $uploadedFilesStack['updateFile'];
			$possFormats=array("xlsx");
			$uplStatus=$this->UploadController->uploadFiles($directory,$uploadedFiles, $possFormats,1);
    		if($uplStatus===1){
			/*************************************************
					Vytiahni dáta z oboch súborov
			*************************************************/
    			$this->AdminController->configuration($this->resources.'/secXML/xlsxConf.xml');

				foreach (glob($this->resources."/tempXLSXS/*") as $index => $newFileAdr) {

					$newRows=$delRows=$updateRows=$newI=$updateIndex=array();
					$fileName = explode(".",basename($newFileAdr))[0];
					
					if (empty(glob($this->resources."/xlsxs/".$fileName.".xlsx")))
						continue;

					$newFileData=$this->AdminController->XLSXSFirstData($newFileAdr);
					$oldFileData=$this->AdminController->XLSXSFirstData($this->resources."/xlsxs/".basename($newFileAdr));
					$oldSQLData=$this->AdminController->loadXLSXs($this->resources, "xlsxs" ,"noAccFiles",$fileName);  
				
			/*************************************************
					Zadanie countera
			*************************************************/
				$newCount = sizeof($newFileData);
				$oldCount = sizeof($oldFileData);
				$counter =($newCount>=$oldCount)?$newCount:$oldCount;
				for ($i=0; $i < $counter ; $i++) {
					
					$SQLkey = $this->OtherModel->findIndex($oldSQLData[$index], 'rowNO',($i+1));

					$newDateDiff=$this->OtherModel->dateDiff($newFileData[$i]["endDate"]);
					$oldDateDiff=$this->OtherModel->dateDiff($oldFileData[$i]["endDate"]);
	
	/*
var_dump($newDateDiff);
echo "<br><br>";
var_dump($oldDateDiff);
echo "<br><br>";
echo "<br><br>";
die();
		/*************************************************
				V prípade, že existuje starý riadok a nový nie (odstráň)
		*************************************************/

					if (($oldFileData[$i]["empty"]==0)&&($newFileData[$i]["empty"]==1)) {
							if (($oldSQLData[$index][$SQLkey]["archived"]==0)&&$oldDateDiff>30)
							array_push($delRows, $i+1);
						//odstráň riadky
							continue;
					}
		/*************************************************
				V prípade, že existuje nový riadok a starý nie (pridaj)
		*************************************************/
		
					if (($newFileData[$i]["empty"]==0)&&($oldFileData[$i]["empty"]==1)) {
						if ($newDateDiff>30){	
							array_push($newRows, $newFileData[$i]);
							array_push($newI, $i+1);
						}
							// môže byť opatrené tým, že všetko do konca bude pridané
							continue;
					}
		/*************************************************
				V prípade, že existuje starý aj nový riadok 
		*************************************************/
					if (($newFileData[$i]["empty"]==0)&&($oldFileData[$i]["empty"]==0)) 
						if ($newDateDiff>30&&$oldDateDiff>30)
							if ($newFileData[$i] != $oldFileData[$i]) {
								array_push($updateRows, $newFileData[$i]);
								$updateRows[sizeof($updateRows)-1]["nameArr"]=$oldSQLData[$index][$SQLkey]["nameArr"];
								$updateRows[sizeof($updateRows)-1]["emailArr"]=$oldSQLData[$index][$SQLkey]["emailArr"];
								array_push($updateIndex, $i+1);
							}
						
				}
/*
var_dump($newRows);
echo "<br><br>";
var_dump($delRows);
echo "<br><br>";
var_dump($updateRows);
echo "<br><br>";
				/***********************
					Pridaj nové riadky
				************************/
				if (!empty($newRows)) 
					
					$this->AdminController->addXLSXRow($this->resources,$fileName, $newRows, $oldCount,$newI);
				/***********************
					Odstráň riadky
				************************/
				if(!empty($delRows))
					$this->AdminController->delXLSXRow($this->resources,$fileName, $delRows);
				/***********************
				  Zameň riadky za nové
				************************/

				if(!empty($updateRows))
					$this->AdminController->editXLSXRow($this->resources,$fileName,$updateRows,$updateIndex);

			}
			
			$this->AdminController->delete($this->resources."/tempXLSXS/".$fileName);
				
			}

		}

		if ($section=="fileConfirm") {
			$confName = $post["confName"];
			$confFile=glob($this->resources."/xlsxs/".$confName."*")[0];
			if(rename($confFile, $this->resources."/uhrad_xlsxs/".date("ymd").basename($confFile))){
			$confPaymentFolder=glob($this->resources."/neov_platby/".$confName)[0];
			if(rename($confPaymentFolder, $this->resources."/uhrad_platby/".date("ymd").$confName)){
				$this->session->set('alert', 1);
			}
			else $this->session->set('alert', 0);
			}
		else $this->session->set('alert', 0);
		}
	}
	return $response->withRedirect($data['baseurl']."/admin");
})->add(new \Slim\Middleware\Session($sessSettings));

$app->get('/admin[/{section}[/{adds}]]', function ($request, $response,$args) use ($data) {
	if (isset($this->session->alert)) include_once $this->resources.'/alert.php';

	$data['baseurl'] = $request->getUri()->getBaseUrl();
	if (!$this->LoginModel->login_check($this->LoginController,$this->session,$this->LoginController->settedPass,$this->LoginController->locked)) 
		return $response->withRedirect($data['baseurl']);	
	$this->AdminController->configuration($this->resources. '/secXML/xlsxConf.xml');
			$data["bookCount"]=$this->AdminController->loadXLSXs($this->resources,"xlsxs" ,"newFilesCount");
			$data["expirCount"]=$this->AdminController->loadXLSXs($this->resources,"xlsxs" ,"expiredCount");

	$data["permitAble"]=1;
	$data["preferDown"]=0;
	$data["masta"]=$this->LoginController->masta;

	$this->view->render($response, 'inc/_top.phtml');

	$modalData["modalID"]="previewMailModal";
	$modalData["modalName"]="Ukážka mailu";
	$this->view->render($response, 'inc/_modal.php',$modalData);

	$XLSXModalData["modalID"]="XLSXModal";
	$XLSXModalData["modalName"]="<span class='clr-org font-weight-bold'>ABC</span>";
	$XLSXModalData["modalCont"] = file_get_contents($this->resources."/modalContents/modalCont_dataModal.phtml");
	$this->view->render($response, 'inc/_modal.php',$XLSXModalData);
	
	$modalData["modalID"]="investFilesModal";
	$modalData["modalName"]="Súbory od investora";
	$this->view->render($response, 'inc/_modal.php',$modalData);
	
	if (isset($args["section"])) {
		$section=$args["section"];
		if ($section=="home") {
			$data["typ"] = "xlsxs";
			$xlsxData=$this->AdminController->loadXLSXs($this->resources,$data["typ"],"home");
	$data["preparedData"]=$this->AdminController->prepareData($xlsxData);
	$this->view->render($response, 'views/admin/home.phtml',$data);
	return $this->view->render($response, 'inc/_bottom.phtml');
		}
		if ($section=="settings") { 

			if (isset($args["adds"])) {
				if ($args["adds"]=="xy"){
					$data["helper"] = '<p>1. Zadaj X <p><p>2. Zadaj Y<p>';
					$data["active"] = $args["adds"];
					$data["addFile"]=$this->resources."/views/admin/setFileXY.phtml";
					$data["XML"]=$this->AdminController->conf;
				}
				elseif (($this->LoginController->masta==1)&&$args["adds"]=="mastaAccs"){

					$data["helper"] = '<p>1. Zadaj X <p><p>2. Zadaj Y<p>';
					$data["active"] = $args["adds"];
					$data["accsForMasta"]=$this->LoginController->allAccData();
					$data["addFile"]=$this->resources."/views/admin/mastaAccs.phtml";
					$data["XML"]=$this->AdminController->conf;
				}
				if ($args["adds"]!="acc"&&($args["adds"]=="mastaAccs"&&$this->LoginController->masta==0)&&$args["adds"]!="xy") {
						return $response->withRedirect("./acc");
							}			
			}
			if (!isset($args["adds"])||$args["adds"]=="acc") {
					$newAccData["modalName"]="Nový účet";
					$newAccData["modalID"]="newAcc";
					$newAccData["modalCont"] = file_get_contents($this->resources."/modalContents/modalCont_newAcc.phtml");
					$this->view->render($response, 'inc/_modal.php',$newAccData);
					$data["XML"]=$this->LoginController->settedMail;
					$data["helper"] = '<p>1. Zadaj meno <p><p>2. Zadaj heslo<p>';
					$data["active"] = "acc";
					$data["addFile"]=$this->resources."/views/admin/setFileAcc.phtml";
			}

			
			
			$this->view->render($response, 'views/admin/settings.phtml',$data);
	return $this->view->render($response, 'inc/_bottom.phtml');
		}
		if ($section=="book") {
			$data["permitAble"]=0;
			$data["preferDown"]=1;
			$data["typ"] = "xlsxs";
			$xlsxData=$this->AdminController->loadXLSXs($this->resources,$data["typ"],"book");
			$data["preparedData"]=$this->AdminController->prepareData($xlsxData);

			$data["newFilesTyp"] = "xlsxs";
			$newFilesData=$this->AdminController->loadXLSXs($this->resources,$data["newFilesTyp"],"newFiles");
			$data["newFilesPrepData"]=$this->AdminController->prepareData($newFilesData);
			$this->view->render($response, 'inc/_top.phtml');

	$modalData["modalID"]="investFilesModal";
	$modalData["modalName"]="Súbory od investora";
	$this->view->render($response, 'inc/_modal.php',$modalData);
	
	$this->view->render($response, 'views/admin/home.phtml',$data);
	return $this->view->render($response, 'inc/_bottom.phtml');
		}
		if ($section=="charts") {
			if (isset($args["adds"])) {
				if ($args["adds"]=="pre_init"){
					/*************************
						Pred 1. emailom
					*************************/
					$data["typ"] = "xlsxs";
					$XLSXpodm = $args["adds"];
				}
				if ($args["adds"]=="init"){
					/*************************
						Pred 1. emailom
					*************************/
					$data["typ"] = "xlsxs";
					$XLSXpodm = $args["adds"];
				}
				if ($args["adds"]=="notif1"){
					/*************************
						Po 1. emaili
					*************************/
					$data["typ"] = "xlsxs";
					$XLSXpodm = $args["adds"];
				}
				if ($args["adds"]=="notif2"){
					/*************************
						Po potvrdení
					*************************/
					$data["typ"] = "xlsxs";
					$XLSXpodm = $args["adds"];
					$data["permitAble"]=0;
					$data["preferDown"]=1;
				}
			}
			else{
				$data["typ"] = "xlsxs";
				$XLSXpodm = "init";
			}
			$data["active"]=isset($args["adds"])?$args["adds"]:"init";
	$xlsxData=$this->AdminController->loadXLSXs($this->resources,$data["typ"], ((isset($XLSXpodm))?$XLSXpodm:NULL));
			$data["preparedData"]=$this->AdminController->prepareData($xlsxData);
	
			$this->view->render($response, 'views/admin/charts.phtml',$data);
			return $this->view->render($response, 'inc/_bottom.phtml');
		}
		if ($section=="expired"){
					/*************************
						Po skončení platnosti
					*************************/
					$data["typ"] = "xlsxs";
					$data["permitAble"]=0;
					$data["preferDown"]=1;
					$XLSXpodm = "expired";
				}
		if ($section=="update") {
			return $this->OtherModel->drawBasicPage($this->view,$response,$data,'views/admin/update.phtml');
		}
		if ($section=="logout") {

			$this->LoginController->logOut();
		return $response->withRedirect($data['baseurl']);		
		}
	}else{
	$data["typ"] = "xlsxs";
	$XLSXpodm = "home";
	}
	$xlsxData=$this->AdminController->loadXLSXs($this->resources,$data["typ"], ((isset($XLSXpodm))?$XLSXpodm:NULL));
			$data["preparedData"]=$this->AdminController->prepareData($xlsxData);

	
	$this->view->render($response, 'views/admin/home.phtml',$data);
	return $this->view->render($response, 'inc/_bottom.phtml');
})->add(new \Slim\Middleware\Session($sessSettings));
				/********************************
					download
				********************************/

$app->get('/download', function ($request, $response,$args) use ($data) {
	$data['baseurl'] = $request->getUri()->getBaseUrl();
	$get = $request->getQueryParams();
	if (!$this->LoginModel->login_check($this->LoginController,$this->session,$this->LoginController->settedPass,$this->LoginController->locked)) 
		return $response->withRedirect($data['baseurl']);
			//stiahnutie 1 súboru
		if (isset($get["fileNO"])) 
		$this->OtherModel->downloadZIP($get["name"],$get["area"],$this->resources,$this->DbController,$get["fileNO"]);
			//stiahnutie všetkých súborov
		else
		$this->OtherModel->downloadZIP($get["name"],$get["area"],$this->resources,$this->DbController);
    
})->add(new \Slim\Middleware\Session($sessSettings));
    
				/********************************
					Stránka prihlásenia
				********************************/

$app->post('/', function ($request, $response) use ($data) {
	if (isset($this->session->alert)) include_once $this->resources.'/alert.php';

    $post=$request->getParsedBody();
		$data['baseurl'] = $request->getUri()->getBaseUrl();
		

	if ($this->LoginModel->login_check($this->LoginController,$this->session,$this->LoginController->settedPass,$this->LoginController->locked))
		return $response->withRedirect($data['baseurl']."/admin");

    if (isset($post["email"],$post["p"]))
	    if($this->LoginController->run($post["email"],$post["p"],$this->resources."/secXML/IPbrutes.xml", $this->resources.'/mail', "OrigSessVal")){
	    	if ($this->session->sent==0) {
	    		$this->session->set('checkString', $this->LoginModel->createCheckString());
	    		
	    		$emailData["nadpis"] = "Potvrdzovací kód";
	    		$emailData["oslovenie"] = "Vážený administrátor";
	    		$emailData["content"]="Váš prihlasovací kód: ".$this->session->checkString;
	    		
	    		$email=$this->LoginController->settedMail;
	    		$subject="Potvrdzovací kód";
				$headers = EMAIL_HEADERS. 'From: <probim@probim.sk>' . "\r\n";
        
        		$message=$this->OtherModel->getmailData($this->resources,"expirMail",NULL,$emailData);
	    		if(mail($email,$subject,$message[0],$headers)){
		    		//Zabráni viacnásobnému generovaniu kódu
		    		$this->session->set('sent', 1);
		    	}
		    	else{
		    		return "Vznikla chyba pri odoslaní emailu";
		    	}
	    	}   

	    	$this->OtherModel->drawBasicPage($this->view,$response,$data,'views/mailConfirm.phtml');
				return $response;
			}
			//else return 0; // status fatalnej chyby
	    

	if (isset($post["checkString"])) {
			$data["session"] = $this->session->get('checkString');
		if ($this->LoginController->confirm($post["checkString"])) {
			
			$this->session->delete('checkString');
			return $response->withRedirect($data['baseurl']."/admin");
		}
		else{
			$this->OtherModel->drawBasicPage($this->view,$response,$data,'views/mailConfirm.phtml');
			return $response;
		} 
	}

		$this->session->delete('checkString');
		$this->session->delete('sent');
		$this->session->delete('fake_login_string');
		
	return $response->withRedirect($data['baseurl']);
			

})->add(new \Slim\Middleware\Session($sessSettings));


$app->get('/', function ($request, $response) use ($data) {
	if (isset($this->session->alert)) include_once $this->resources.'/alert.php';

	$this->session->set('sent', 0);
		$data['baseurl'] = $request->getUri()->getBaseUrl();
if ($this->LoginModel->login_check($this->LoginController,$this->session,$this->LoginController->settedPass,$this->LoginController->locked)) {
		$data['baseurl'] = $request->getUri()->getBaseUrl();
		return $response->withRedirect($data['baseurl']."/admin");
	}
	$this->OtherModel->drawBasicPage($this->view,$response,$data,'views/login.phtml');
	return $response;


})->add(new \Slim\Middleware\Session($sessSettings));
?>