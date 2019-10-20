<?php
//header('Content-Type: application/json');
include("../include/config.php");
$mysqli = new db();
include("../include/lang.inc.php");


if(!empty($_GET["fish_general_id"] and intval($_GET["fish_general_id"])) and !empty($_GET["lang"]) and strval($_GET["lang"])){
	
	$q = intval($_GET['fish_general_id']);
	$language = strval($_GET['lang']);
	if($language == "de"){
		$language = "de";
	}else if($language == "en"){
		$language = "en";
	}else{
		$language = "de";
	}
	
	
	
	$fish_info = array();
	
	// Ermittelt alle Arten die (nicht) verzert werden können
	$sSQL = "SELECT fish_general_id, fish_general_name, status_id_fk, "
		  . "fish_species_rel_name_lat "
		  . "FROM bc_fish_general "
		  . "INNER JOIN bc_fish_species_rel "
		  . "ON bc_fish_general.fish_general_id = bc_fish_species_rel.fish_general_id_fk "
		  . "WHERE bc_fish_general.fish_general_id=?";
	
	//echo $sSQL;
	

	$articleResult = __prepareStmtArticle($mysqli, $sSQL, $q);
	$fish_info = "";
	$sEntry = "";
	$fish_status = "";
	
	while($zeile = $articleResult->fetch_assoc()){
	
		/*echo empty($fish_info);
		echo $zeile["status_id_fk"];*/
		
		if(empty($fish_info) and $zeile["status_id_fk"] == 2){
			$fish_status = false;
			$fish_info .= "<li class='list-group-item list-group-item-danger'>" . $lang[$language]['Not sustainable'] . "'" . $zeile["fish_general_name"] . "'</li>";
		}else if(empty($fish_info) and $zeile["status_id_fk"] == 1){
			$fish_status = true;
			$fish_info = "<li class='list-group-item list-group-item-success'>" . $lang[$language]['Sustainable'] . "'" . $zeile["fish_general_name"] . "'</li>";
		}
		
		$sEntry = "<li class='list-group-item'>" . $zeile["fish_species_rel_name_lat"] . "</li>";
		$fish_info .= $sEntry;
		
	}
	
	$fish_info = "<div class='list-group'>" . $fish_info . "</div>";
	
	
	$sSQL = "SELECT bc_fishing_method_type.fishing_method_type_name, "
		  . "bc_fish_general.fish_general_name, "
		  . "bc_fishing_place.fishing_place_name, "
		  . "bc_fishing_place.fishing_sub_place, "
		  . "bc_fishing_place.fishing_catch_place "
		  . "FROM bc_fishing_place "
		  . "INNER JOIN bc_fishing_method_type "
		  . "ON bc_fishing_place.fishing_method_type_id_fk=bc_fishing_method_type.fishing_method_type_id "
		  . "INNER JOIN bc_fish_general "
		  . "ON bc_fishing_place.fish_general_id_fk=bc_fish_general.fish_general_id "
		  . "WHERE bc_fish_general.fish_general_id=?";
	
	//echo $sSQL;
	$articleResult = __prepareStmtArticle($mysqli, $sSQL, $q);
	
	$detailLog = "";
	$fish_general_name = "";
	
	while($zeile = $articleResult->fetch_assoc()){
		
		/*$sEntry[0] = $zeile["fishing_method_type_name"];
		$sEntry[1] = $zeile["fish_general_name"];
		$sEntry[2] = $zeile["fishing_place_name"];
		$sEntry[3] = $zeile["fishing_sub_place"];
		$sEntry[4] = $zeile["fishing_catch_place"];*/
		
		if($fish_status == true){
			$detailLog .= "<div class='panel panel-danger'>";
		}else if($fish_status == false){
			$detailLog .= "<div class='panel panel-success'>";
		}
		
		if(empty($fish_general_name)){
			$fish_general_name = $zeile["fish_general_name"];
			if($fish_status == false){
				$detailLog .= "<div class='panel-heading'>" . $lang[$language]['Sustainable'] . "'" . $zeile["fish_general_name"] . "'</div>";
			}else if($fish_status == true){
				$detailLog .= "<div class='panel-heading'>" . $lang[$language]['Not sustainable'] . "'" . $zeile["fish_general_name"] . "</div>";
			}
		}
		
		$detailLog .= "<table class='table'>";
		$detailLog .= "<tr><th>" . $lang[$language]['Method'] . "</th><td>" . $zeile["fishing_method_type_name"] . "</td></tr>";
		$detailLog .= "<tr><th>" . $lang[$language]['Place'] . "</th><td>" . $zeile["fishing_place_name"] . "</td></tr>";
		if(!empty($zeile["fishing_sub_place"])){
			$detailLog .= "<tr><th>" . $lang[$language]['Sub place'] . "</th><td>" . $zeile["fishing_sub_place"] . "</td></tr>";
		}
		$detailLog .= "<tr><th>" . $lang[$language]['Catch'] . "</th><td>" . $zeile["fishing_catch_place"] . "</td></tr>";
		
		$detailLog .= "</table";
		$detailLog .= "</div>";
		
	}
	
	
	
	$fish_info .= $detailLog;
	
	echo $fish_info;
}

//
// Methode : Funktion für die Erstellung eines prepared Statements
//         : an die Artikel-Datenbank.
function __prepareStmtArticle($mysqli, $sSQL, $sParam) {
		//echo "KAKA:" . $sParam;
		$resultArticle = $mysqli->prepare($sSQL);

			
		$resultArticle->bind_param('i', $sParam);
	
		//echo $sSQL . "<br />";
		//echo $sParam . "<br />";
	
		try {
			$resultArticle->execute();
		} catch (Exception $e){
			echo "Fehler in der SQL Anweisung.";
		}
	
		$result = $resultArticle->get_result();

		return $result;
}



?>