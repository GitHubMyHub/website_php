<?php
include("../include/config.php");
$mysqli = new db();

if(isset($_GET["user_id"]) && !empty($_GET["user_id"]) 
   						   && isset($_GET["sFilter"]) && !empty($_GET["sFilter"])
   						   && isset($_GET["bChecked"]) && !empty($_GET["bChecked"])){
	
	$user_id = strval($_GET["user_id"]);
	//$sFilter = strval($_GET["sFilter"]);
	$sFilter = urldecode($_GET["sFilter"]);
	$bChecked = $_GET['bChecked'] == 'true' ? true : false;
	
	/*echo $user_id;
	echo $sFilter;
	echo $bChecked;*/
	
	
	/*$sSQL = "SELECT * FROM bc_filters WHERE filters_name='" . $sFilter . "'";
	$result = $mysqli->query($sSQL);*/
	
	$sSQL = "SELECT * FROM bc_filters WHERE filters_name=?";
	$stmt = $mysqli->prepare($sSQL);
	$stmt->bind_param("s", $sFilter);
	
	$stmt->execute();
	$result = $stmt->get_result();
	
	//echo $sSQL;
	//print_r($result);
	
	if($result->num_rows > 0){
		$zeile = $result->fetch_assoc();
		
		/*$sSQL = "SELECT * FROM bc_filters_rel WHERE user_id_fk='" . $user_id . "' AND filters_id_fk=" . $zeile["filters_id"];
		$result = $mysqli->query($sSQL);*/
		
		$sSQL = "SELECT * FROM bc_filters_rel WHERE user_id_fk=? AND filters_id_fk=?";
		$stmt = $mysqli->prepare($sSQL);
		$stmt->bind_param("si", $user_id, $zeile["filters_id"]);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		//echo $sSQL;
		//print_r($result);
		/*if($bChecked == true){
			echo "Wahr";
		}else{
			echo "Falsch";
		}*/
		
		if($result->num_rows == 0 && $bChecked == true){
			/*$sSQL = "INSERT INTO bc_filters_rel (user_id_fk, filters_id_fk) VALUES ('" . $user_id . "', " . $zeile["filters_id"] . ")";
			$mysqli->query($sSQL);*/
			/*echo "JAAAA";
			echo $user_id;
			echo $zeile["filters_id"];*/
			
			$sSQL = "INSERT INTO bc_filters_rel (user_id_fk, filters_id_fk) VALUES (?, ?)";
			$stmt = $mysqli->prepare($sSQL);
			$stmt->bind_param("si", $user_id, $zeile["filters_id"]);
			$stmt->execute();
			
			//echo $sSQL;
			//print_r($stmt);
		}else if($result->num_rows > 0 && $bChecked == false){
			/*$sSQL = "DELETE FROM bc_filters_rel WHERE user_id_fk='" . $user_id . "' AND filters_id_fk=" . $zeile["filters_id"];
			$mysqli->query($sSQL);*/
			
			$sSQL = "DELETE FROM bc_filters_rel WHERE user_id_fk=? AND filters_id_fk=?";
			$stmt = $mysqli->prepare($sSQL);
			$stmt->bind_param("si", $user_id, $zeile["filters_id"]);
			$stmt->execute();
			
			//echo $sSQL;
		}
		
	}
}else if(isset($_GET["user_id"]) && !empty($_GET["user_id"])){
	$user_id = strval($_GET["user_id"]);
	
	/*$sSQL = "SELECT * FROM bc_filters_rel INNER JOIN bc_filters ON bc_filters_rel.filters_id_fk = bc_filters.filters_id WHERE user_id_fk='" . $user_id . "' ORDER BY filters_id ASC";
	$result = $mysqli->query($sSQL);*/
	

	$sSQL = "SELECT * FROM bc_filters_rel "
	      . "INNER JOIN bc_filters "
		  . "ON bc_filters_rel.filters_id_fk = bc_filters.filters_id "
		  . "WHERE user_id_fk=? ORDER BY filters_id ASC";
	$stmt = $mysqli->prepare($sSQL);
	$stmt->bind_param("s", $user_id);
	
	$stmt->execute();
	
	$result = $stmt->get_result();

	//echo $sSQL;
	
	$resultJson = array();
	$int = 1;
	
	while($zeile = $result->fetch_assoc()){
		//echo $zeile["filters_rel_id"];
		//echo $zeile["filters_id_fk"];
		//echo $zeile["filters_name"] . "<br />";
		$resultJson[$int] = array($zeile["filters_id"] => $zeile["filters_name"]);
		$int++;
	}
	
	echo json_encode($resultJson);
	
}

?>