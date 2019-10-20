<?php

class db extends mysqli{
	
	// Hostname	
	private $host = "localhost";

	// Username
	private $username = "root";

	// Password
	private $password = "root";

	// Datenbank
	private $datenbank = "barcode2";

	public function __construct(){
		parent::__construct($this->host, $this->username, $this->password, $this->datenbank);
		
		//print_r(parent::$connect_error);

		// Prüft auf Verfügbarkeit einer Datenbank Verbindung
		/*if(parent::connect_error) {
			echo "Fehler bei der Verbindung: " . mysql_connect_error();
			exit();
		}*/
		
        if( mysqli_connect_errno() ) {
            throw new exception("Fehler bei der Verbindung: " . mysqli_connect_error() . " " . mysqli_connect_errno()); 
        }
		
		if(!parent::set_charset("utf8")){
			throw new Exception("Fehler beim laden von UTF-8 " . parent::error);
		}

		// Prüft ob das Charset UTF-8 ist
		/*if(!$mysqli->set_charset("utf8")) {
			echo "Fehler beim laden von UTF-8 " . $mysqli->error;
		}*/

	}
	
	public function query($sSQL, $resultmode = MYSQLI_STORE_RESULT){
		return parent::query($sSQL, $resultmode);
	}
	
	public function prepare($sSQL){
		return parent::prepare($sSQL);
	}
	
	public function bind_param($dataValue, $params){
		parent::bind_param($dataValue, $params);
	}
	
	public function execute(){
		return parent::execute();
	}

}

?>