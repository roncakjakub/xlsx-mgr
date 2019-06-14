<?php
	if ($this->session->alert===1) {    	
		unset($incl);
		$data["clrMod"]="modal-success";
		$data["modalName"]="Akcia prebehla úspešne";
        $data["modalID"]="notifOKMod";
        $this->view->render($response, 'inc/_modal.php',$data);
    }
    else if($this->session->alert===0){
		unset($incl);
		$data["clrMod"]="modal-error";
		$data["modalName"]="Akcia prebehla neúspešne";
		$data["modalID"]="notifOKMod";
        $this->view->render($response, 'inc/_modal.php',$data);		
    }
    else{
    	unset($incl);
		$data["clrMod"]="modal-warning";
		$data["modalName"]="Varovanie";
		$data["modalID"]="notifOKMod";
		$data["modalCont"] = $this->session->alert; 
        $this->view->render($response, 'inc/_modal.php',$data);	
    }
?>