<?php 
class UploadController
{
	private $name,$text,$cronRoute,$otherModel;
	public function __construct($om){
		$this->otherModel = $om;
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
	    $this->otherModel->createUploadXML(0, $adress."/downloadedFiles.xml","down",0);
	    $this->otherModel->createUploadXML(0, $adress."/downloadedFiles.xml","archiv",1);
    }
    if ($errArr!=="") 
        return $errArr;
    return 1;
    }
     
}