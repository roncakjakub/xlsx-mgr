<?php 
 class LoginController
{
	private $session, $loginModel,$BruteGroup, $checkBruteFile, $insertedPass, $sessName, $mailFileRoute, $userFile,$userFileAdr;
	public $settedMail, $locked,$masta, $unlockKey, $settedPass;
	
	public function __construct($session,$userFile){
		$this->session=$session;
    	$this->loginModel = new LoginModel($session);
    	$this->$userFileAdr = $userFile;
		$this->userFile=simplexml_load_string(file_get_contents($userFile));
		if (isset($this->session->accI)) $this->setAccData($this->session->accI);
}
	public function setAccData($index=NULL){
		if ($index!==NULL) {
		$id=0;
		foreach ($this->userFile->user as $acc) {
			if ($index==$id) {
				$this->settedMail=(string)$acc->mail;
				$this->settedPass=(string)$acc->pass;
				$this->locked    =(string)$acc->locked;
				$this->masta    =(string)$acc->masta;
				$this->unlockKey =(string)$acc->unlockKey;
				return $id;
			}
			$id++;
			}
		return false;
		}
		return false;	
	}
	public function run($insertedEmail, $insertedPass, $checkBruteFile, $mailFileRoute, $sessName){
		$this->checkBruteFile=$checkBruteFile;
		$this->insertedPass=$insertedPass;
		$bruteFile=simplexml_load_string(file_get_contents($checkBruteFile));
		$this->BruteGroup=$bruteFile->group;
		$this->mailFileRoute=$mailFileRoute;
		$this->sessName=$sessName;
		$index=0;
		foreach ($this->userFile->user as $acc) {
			if ($insertedEmail==$acc->mail) {
				$this->settedMail=(string)$acc->mail;
				$this->settedPass=(string)$acc->pass;
				$this->locked    =(string)$acc->locked;
				$this->unlockKey =(string)$acc->unlockKey;
				break;
			}
			$index++;
		}
		if (!isset($this->settedMail)||$this->locked==1) {
			return 0;
		}
		if($this->loginModel->login($this->session,$this->checkBruteFile, $this->insertedPass, $this->settedMail, $this->settedPass, $this->sessName,$index)){

        return 1;
		}else 
			if($attIP=$this->loginModel->checkbrute($this->BruteGroup, 30, 8,$index)){
				otherModel::accountLocking($this->$userFileAdr,1,$index);
				$ownText="Niekto s IP ".$attIP. " chcel vstúpiť na stránku cez Váš email v krátkom časovom období. Účet je zablokovaný. Pre odblokovanie stlačte tlaćidlo nižšie.";
				$emailData["link"] = $this->unlockKey."&id=".$index;
				$emailData["nadpis"] = "Varovanie";
				$emailData["oslovenie"]= "Administrátor";
				
				$headers = EMAIL_HEADERS. 'From: <probim@probim.sk>' . "\r\n";

				$message=$this->OtherModel->getmailData($this->resources,"disableAcc",$row,$emailData);

				mail($this->settedMail, "Opakovaná neúspešná snaha o vniknutie na stránku.",$message,$headers);
			} return 0;
	}

	public function setSession($session){
		$this->session=$session;

	}

	public function confirm($insertedString){
    	if ($insertedString==$this->session->checkString) {
		$cookie_name = "p";
    	$cookie_value = $this->session->fake_login_string;
    	$this->session->set('login_string',$this->session->fake_login_string);
    	$this->session->delete("fake_login_string");
    	return 1;
		}else {
    	//$this->session->delete("fake_login_string");
			return false;
		}
	}
	public function getEmails(){
		$emailArr=array();
		foreach ($this->userFile as $acc) {
			array_push($emailArr, $acc->mail);
		}
		return $emailArr;
	}
	public function allAccData(){
		return $this->userFile;
	}
	public function logOut() {
		$this->session->delete('accI');
		// Unset all session values 
		$_SESSION = array();
		 
		// get session parameters 
		$params = session_get_cookie_params();
		 
		// Delete the actual cookie. 
		setcookie(session_name(),
		        '', time() - 42000, 
		        $params["path"], 
		        $params["domain"], 
		        $params["secure"], 
		        $params["httponly"]);

		unset($_COOKIE['p']);
		setcookie('p', null, -1, '/');
		

		// Destroy session 
		//session_destroy();

}	
}