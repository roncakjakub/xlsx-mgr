<?php 
class UploadController
{
	private $name,$text,$cronRoute,$otherModel,$dbCtrl;
	public function __construct($om,$dbCtrl){
        $this->otherModel = $om;
		$this->dbCtrl = $dbCtrl;
	}
	public function init($ascii,$cronRoute){
			$this->text=otherModel::fromASCII($ascii);
			$this->cronRoute=$cronRoute;
	}
	public function nameCheck(){
		if (!empty(glob($this->cronRoute."/xlsxs/".$this->text.".xlsx"))) {
			$this->name=glob($this->cronRoute."/xlsxs/".$this->text.".xlsx")[0];
			return $this->name;
		}
		else return 0;//odosli adminovi email s hlásenim o subore
	}
	public function uploadFiles($dir,$subory,$possFormats,$update,$nazov=NULL,$area=NULL){
    $adress=($update==0)?($dir."/".$nazov."/".$area):$dir;
    
    $errArr ="";
    foreach ($subory as $key => $uploadedFile) {
        if ($uploadedFile->getError() === UPLOAD_ERR_OK||$uploadedFile->getError() === 0) {
            $format=end(explode(".", $uploadedFile->getClientFilename()));
            if (!in_array($format, $possFormats)){
                $errArr .=$uploadedFile->getClientFilename()." ";
                continue;
            }
            $newName=($update==0)?("f".(sizeof(glob($adress."/*"))+$key+1)."_"):"";
            $uploadedFile->moveTo($adress."/".$newName.$uploadedFile->getClientFilename());
        }
        else $errArr.=$uploadedFile->getClientFilename()." ";
    }
    if ($update==0) {
        //zmena konf. dát riadka
    $rowID=$this->dbCtrl->getID("dataview","nazov=".$nazov." and rowNO = ".$area);
    $this->dbCtrl->update("riadky",array("downloaded", "archived"),array(0,1),"ID=".$rowID);
    }
    if ($errArr!=="") 
        return $errArr;
    return 1;
    }
     
}