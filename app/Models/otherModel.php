<?php

class OtherModel{


    private $session;

    /**
     * @param $text
     * @return string
     */
    public static function toASCII($text){
 		$ascii="";
 		foreach (unpack("C*", $text) as $value)
		$ascii.=($value-20);
		return $ascii;
	}

    /**
     * @param $ascii
     * @return string
     */
    public static function fromASCII($ascii){
 		$text="";
 		foreach (str_split($ascii, 2) as $char)
		$text.=chr($char+20);
			return $text;
 	}

    /**
     * @param $file
     * @param $stav
     * @param $index
     * @param null $newKey
     * @return int
     */
    public function accountLocking($file, $stav, $index, $newKey=NULL){
        libxml_disable_entity_loader(false);
 		$doc = new DOMDocument();
		$doc->load($file);

        $users=$doc->getElementsByTagName("user");
        $currUser=$users->item($index);
        $currUser->getElementsByTagName("locked")->item(0)->nodeValue = $stav;
        if($newKey!=NULL)
        $currUser->getElementsByTagName("unlockKey")->item(0)->nodeValue = $newKey;
		return ($doc->save($file))?1:0;
 	}
    public function createUploadXML($new,$adr,$change=NULL,$value=NULL){
        libxml_disable_entity_loader(false);
        if ($new) {
            $dom = new DomDocument("1.0", "utf-8");
            $root=$dom->createElement('doc');
            $dom->appendChild($root);
            $node=$dom->createElement('downloaded', 0);
            $root->appendChild($node);
            $node=$dom->createElement('archived', 0);
            $root->appendChild($node);
            $node=$dom->createElement('expired', 0);
            $root->appendChild($node);
            $node=$dom->createElement('expirKnow', 0);
            $root->appendChild($node);
            $node=$dom->createElement('deleted', 0);
            $root->appendChild($node);
        }
        else{
            $dom = new DOMDocument("1.0", "utf-8");
            $dom->load($adr);
            if ($change=="down")
            $dom->getElementsByTagName("downloaded")->item(0)->nodeValue = $value;
            if ($change=="archiv") 
            $dom->getElementsByTagName("archived")->item(0)->nodeValue = $value;
            if ($change=="expired") 
                $dom->getElementsByTagName("expired")->item(0)->nodeValue = $value;
            if ($change=="expirKnow") 
                $dom->getElementsByTagName("expirKnow")->item(0)->nodeValue = $value;
            if ($change=="del")
            $dom->getElementsByTagName("deleted")->item(0)->nodeValue = $value;
        }

            return($dom->save($adr))?1:0;//'downloadedFiles.xml' 
    }
    public function sendOwnMail($res,$myMail,$hisMail,$subject,$nadpis,$ownText){
        include_once $res.'/mail/_top.php';
        include_once $res.'/mail/alertMail.php';
        include_once $res.'/mail/_bottom.php';
        if(mail($hisMail, $subject,$message ,$headers))
            return 1;
        else return false;
    }
    
    public function drawBasicPage($view,$response,$data,$midPage ){
        $phtmlRoutes=array("inc/_top.phtml",$midPage,'inc/_bottom.phtml');
            foreach ($phtmlRoutes as $route)
                $view->render($response, $route, $data);
    }
    public function lastsFileCheck($name,$resFolder){
        $count=0;
        $affFiles = array();
        $payFolders = glob($resFolder."/neov_platby/".$name."/*");
        /*******************************
            Nahrané súbory
        *******************************/
        foreach ($payFolders as $folderIndex => $payFolder) {
            $payFolderFiles = glob($payFolder."/*");
            if (sizeof($payFolderFiles)>1){
                $downConf=simplexml_load_string(file_get_contents($payFolder."/downloadedFiles.xml"));     
                if ($downConf->downloaded==1) {
                $count++;
                array_push($affFiles, $folderIndex);
                } 
            }
        }
        /*******************************
            Expirované oblasti
        *******************************/
        $excelObj=PHPExcel_IOFactory::load($resFolder."/xlsxs/".$name.".xlsx");
        $getSheet=$excelObj->getActiveSheet()->toArray(NULL);
        foreach ($getSheet as $key => $row) {
            /***********************************
                Preskoč, ak už o tomto riadku viem
            ***********************************/
            if (in_array($key, $affFiles)) continue;
            $enddate=strtotime(date($row[2]));
            if((($enddate-$today)/60/60/24)<-90){
                $count ++;
                array_push($affFiles, $folderIndex);
            }
            if(sizeof($affFiles)==sizeof($getSheet)){
                rename($resFolder."/xlsxs/".$name.".xlsx", $resFolder."/uhrad_xlsxs/".$name.".xlsx");
                rename($resFolder."/neov_platby/".$name,$resFolder."/uhrad_platby/".$name);    
            }
        }
    }
    public function firstConfXlsx($name,$resFolder,$max,$indexes,$dbCtrl){
        /**************************************
            Vytvorenie priečinkov na súbory
        **************************************/
        if (empty(glob($resFolder.'/neov_platby/'.$name)))
            /*****************************
                    Vytvorí priečinok súboru
                ***************************/
            if(!mkdir($resFolder."/neov_platby/".$name)) return false;
             /*****************************
                    Záznam do DB o súbore
                ***************************/
            if(!$this->dbCtrl->insert("subory","nazov",array($name))) return false;
        for ($i=1; $i <= $max; $i++){
            if (!in_array($i, $indexes)) continue;
            
            if (empty(glob($resFolder."/neov_platby/".$name."/".$i))){
                /*****************************
                    Vytvorí priečinok každého riadku
                ***************************/
                if(!mkdir($resFolder."/neov_platby/".$name."/".$i))return false;
                /*****************************
                    Záznam do DB o investoroch
                ***************************/
                if(!$investorID=$this->dbCtrl->getID("investori","meno=".$name."and email="))
                if(!$fileID=$this->dbCtrl->insert("riadky","$cols",$values))return false;    
                /*****************************
                    Záznam do DB o riadkoch
                ***************************/
                if(!$fileID=$this->dbCtrl->getID("subory","nazov=".$name))return false;
                if(!$fileID=$this->dbCtrl->insert("riadky","$cols",$values))return false;
                if (!$this->createUploadXML(1,$resFolder."/neov_platby/".$name."/".$i."/downloadedFiles.xml"))
                        return false;       
            }
        }
        return 1;
    }
    public function dateDiff($date){
        $today=strtotime(date("Y-m-d"));
        $enddate=strtotime(date($date));
        $explodedDate=explode("/", $endDate);
        $endDate=$explodedDate[1].".".$explodedDate[0].".".$explodedDate[2];
        return (($enddate-$today)/60/60/24);
    }
    public function downloadZIP($name,$area,$resFolder,$fileNO=NULL){
    ob_start();
    $filename= preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $name);
    $xlsxAdr=glob($resFolder."/xlsxs/".$name.".xlsx");
    $files=glob($resFolder."/neov_platby/".$name."/".$area."/*");

    $z = new ZipArchive();
    if($z->open("zip".$filename.'.zip', ZipArchive::OVERWRITE|ZipArchive::CREATE)){
        if (empty($fileNO))
            $z->addFile($xlsxAdr[0],"excel.xlsx");
        
        foreach ($files as $key => $value) {    
            
            if (basename($value)=="downloadedFiles.xml") continue;
            if (!empty($fileNO)&&$fileNO!=$key) continue;

            $z->addFile($value,basename($value));                
        }

        $filename = $z->filename;
        $z->close();

    $this->createUploadXML(0, $resFolder."/neov_platby/".$name."/".$area."/downloadedFiles.xml","down",1);
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $filename);
    $size = filesize($filename);
    $name = basename($filename);
    
    ob_clean();
    ob_end_flush();
    
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        // cache settings for IE6 on HTTPS
        header('Cache-Control: max-age=120');
        header('Pragma: public');
    } else {
        header('Cache-Control: private, max-age=120, must-revalidate');
        header("Pragma: no-cache");
    }   
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // long ago
        header("Content-Type: $mimeType");
        header('Content-Disposition: attachment; filename="' . $name . '";');
        header("Accept-Ranges: bytes");
        header('Content-Length: ' . $size);
        
        readfile($filename);
    unlink($filename);
    exit;
}
}}