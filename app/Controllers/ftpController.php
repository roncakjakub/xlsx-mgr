<?php 
class FTPController
 {
 	private $conn, $login_result, $dir;

 	public function __construct($ip,$port,$user,$pass){

		// Connect to FTP server
		$this->conn = ftp_connect($ip, $port); // Timeout is not set, default is 90 seconds

		// Log into FTP srever
		$this->login_result = ftp_login($this->conn, $user, $pass);

		ftp_pasv($this->conn, true);
 	}

 	public function getDir(){

		// Print details about directory
		// Set what directory name to show
		return ($this->dir == ".") ? "/" : "/" . $this->dir;
 	}

 	public function changeDir($dir){

		$this->dir = ($dir) ? $dir : ".";
 	}

 	public function getFiles(){

		return ftp_nlist($this->conn, $this->dir);


		 /*as $element) {
		    $element_name = str_replace($this->dir . "/", "", $element);

		    // Check if element is a file or a directory
		    // If size is -1 then the element is a directory
		    if(ftp_size($this->conn, $element) == -1) {
		        echo "<a href=\"index.php?dir=" . $element . "\">" . $element_name . "</a><br />";
		    }
		    else {
		        echo "<a href=\"http://" . $element . "\" target=\"_blank\">" . $element_name . "</a><br />";
		    }
		}*/
	}

 	public function __destruct{
		// Close connection
		ftp_close($this->conn);
	}
}
