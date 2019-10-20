<?php

	header('Content-Type: application/json');

	include("../include/config.php");
	$mysqli = new db();

if(!isset($_GET["filter_id"]) && empty($_GET["filter_id"]) 
   	&& isset($_GET["user_id"]) && !empty($_GET["user_id"]) 
    && isset($_GET["filter_id_fk"]) && !empty($_GET["filter_id_fk"])
    && isset($_GET["componentsId"]) && !empty($_GET["componentsId"])) {
	
	// CREATING
	
	$user_id = strval($_GET["user_id"]);
	$filter_id_fk = intval($_GET["filter_id_fk"]);
	$componentsId = intval($_GET["componentsId"]);
	
	$myArticleObj = new stdClass();
	$myArray = array();
	
	$sSQL = "INSERT INTO bc_ingredient_filter "
		  . "(user_id_fk, ingredient_filters_id_fk, components_id_fk) "
		  . "VALUES(?,?,?)";
	//echo $sSQL;
	$stmt = $mysqli->prepare($sSQL);
	
	$stmt->bind_param("sii", $user_id, $filter_id_fk, $componentsId);
	
	
	if($stmt->execute()){
	
		$id = $stmt->insert_id;
		
			
		$array = array("create" => "erfolgreich",
					   "item" => $id);

		array_push($myArray, $array);
	
		$myArticleObj->results = (object) $myArray;
	
		echo json_encode($myArticleObj, JSON_PRETTY_PRINT);
	
	}

}else if(isset($_GET["user_id"]) && !empty($_GET["user_id"]
		 && isset($_GET["filter_id_fk"]) && !empty($_GET["filter_id_fk"])) 
		 && !isset($_GET["filter_id"]) && empty($_GET["filter_id"])
		 && !isset($_GET["delete"]) && empty($_GET["delete"])) {
	
	//SELECT all Entries
	
	$user_id = strval($_GET["user_id"]);
	$filter_id_fk = intval($_GET["filter_id_fk"]);
	
	//$sSQL = "SELECT * FROM bc_ingredient_filter WHERE user_id_fk=? AND ingredient_filters_id_fk=?";
	
	$sSQL = "SELECT ingredient_filter_id, user_id_fk, ingredient_filters_id_fk, "
		  . "components_id_fk, components_name "
		  . "FROM bc_ingredient_filter "
		  . "INNER JOIN bc_components "
		  . "ON bc_ingredient_filter.components_id_fk=bc_components.components_id "
		  . "WHERE user_id_fk=? AND ingredient_filters_id_fk=?";
	//echo $sSQL;
	$stmt = $mysqli->prepare($sSQL);
	$stmt->bind_param("si", $user_id, $filter_id_fk);
	
	$stmt->execute();
	$result = $stmt->get_result();
	
	$myArticleObj = new stdClass();
	//$resultJson = array();
	$myArray = array();
	$int = 1;
	
	
			//array_push($my_data2, $array);
			
	
	while($zeile = $result->fetch_assoc()){
		//echo $zeile["filters_rel_id"];
		//echo $zeile["filters_id_fk"];
		//echo $zeile["filters_name"] . "<br />";
		//$resultJson[$int]
		$array = (object) array("ingredient_filter_id" => $zeile["ingredient_filter_id"],
								  "components_name" => $zeile["components_name"],
								  "user_id_fk" => $zeile["user_id_fk"],
								  "ingredient_filters_id_fk" => $zeile["ingredient_filters_id_fk"],
								  "components_id_fk" => $zeile["components_id_fk"]);
		
		array_push($myArray, $array);
		$int++;
	}
	
	$myArticleObj->results = (object) $myArray;
	
	echo json_encode($myArticleObj, JSON_PRETTY_PRINT);
	
}else if(isset($_GET["filter_id"]) && !empty($_GET["filter_id"]) 
		 && isset($_GET["filter_id_fk"]) && !empty($_GET["filter_id_fk"])
		 && isset($_GET["user_id"]) && !empty($_GET["user_id"])
		 && isset($_GET["componentsId"]) && !empty($_GET["componentsId"])){
	
	//UPDATE
	
	$filter_id = intval($_GET["filter_id"]);
	$filter_id_fk = intval($_GET["filter_id_fk"]);
	$user_id = strval($_GET["user_id"]);
	$componentsId = intval($_GET["componentsId"]);
	
	/*echo $filter_id . "<br />";
	echo $filter_id_fk . "<br />";
	echo $user_id . "<br />";
	echo $componentsId . "<br />";*/
	
	$myArticleObj = new stdClass();
	
	$sSQL = "UPDATE bc_ingredient_filter SET components_id_fk=? "
	   	   . "WHERE user_id_fk=? AND ingredient_filter_id=? AND ingredient_filters_id_fk=?";
	
	$stmt = $mysqli->prepare($sSQL);
	
	$stmt->bind_param("isii", $componentsId, $user_id, $filter_id, $filter_id_fk);
	//print_r($stmt);
	if($stmt->execute()){
		
		$array = array("update" => "erfolgreich");
		
		$myArticleObj->results = $array;
	
		echo json_encode($myArticleObj, JSON_PRETTY_PRINT);
	}
	
}else if(isset($_GET["filter_id"]) && !empty($_GET["filter_id"]) 
		 && isset($_GET["user_id"]) && !empty($_GET["user_id"])){
	
	//DELETE
	
	$filter_id = explode(",", strval($_GET["filter_id"]));
	$user_id =  strval($_GET["user_id"]);
	
	//print_r($filter_id);
	
	$myArticleObj = new stdClass();
	
	$sSQL = "DELETE FROM bc_ingredient_filter WHERE user_id_fk=? AND ingredient_filter_id IN (?)";
	$stmt = $mysqli->prepare($sSQL);
	
	for($i=0;$i<count($filter_id); $i++){
		
		$stmt->bind_param("ss", $user_id, $filter_id[$i]);
		$stmt->execute();

	}
	
	$array = array("delete" => "erfolgreich");

	$myArticleObj->results = $array;

	echo json_encode($myArticleObj, JSON_PRETTY_PRINT);
	
}

?>