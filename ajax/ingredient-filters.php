<?php

	header('Content-Type: application/json');

	include("../include/config.php");
	$mysqli = new db();

if(!isset($_GET["filter_id"]) && empty($_GET["filter_id"]) 
    && isset($_GET["filter_name"]) && !empty($_GET["filter_name"]) 
   	&& isset($_GET["user_id"]) && !empty($_GET["user_id"]) 
    && isset($_GET["status_id"]) && !empty($_GET["status_id"])) {
	
	// CREATING
	
	$filter_name = strval($_GET["filter_name"]);
	$user_id = strval($_GET["user_id"]);
	$status_id = intval($_GET["status_id"]);
	
	$myArticleObj = new stdClass();
	$myArray = array();
	
	$sSQL = "INSERT INTO bc_ingredient_filters "
		  . "(ingredient_filters_name, user_id_fk, ingredient_filters_status_id_fk) "
		  . "VALUES(?,?,?)";
	
	$stmt = $mysqli->prepare($sSQL);
	
	$stmt->bind_param("ssi", $filter_name, $user_id, $status_id);
	
	if($stmt->execute()){
		
		$id = $stmt->insert_id;
		
			
		$array = array("create" => "erfolgreich",
					   "item" => $id);

		array_push($myArray, $array);
	
		$myArticleObj->results = (object) $myArray;
	
		echo json_encode($myArticleObj, JSON_PRETTY_PRINT);
	
	}

}else if(!isset($_GET["filter_id"]) && empty($_GET["filter_id"]) 
		 && isset($_GET["user_id"]) && !empty($_GET["user_id"]) 
		 && !isset($_GET["delete"]) && empty($_GET["delete"])) {
	
	//SELECT all Entries
	
	$user_id = strval($_GET["user_id"]);
	
	$sSQL = "SELECT * FROM bc_ingredient_filters WHERE user_id_fk=?";
	$stmt = $mysqli->prepare($sSQL);
	$stmt->bind_param("s", $user_id);
	
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
		$array = (object) array("ingredient_filters_id" => $zeile["ingredient_filters_id"],
								  "ingredient_filters_name" => $zeile["ingredient_filters_name"],
								  "user_id_fk" => $zeile["user_id_fk"],
								  "ingredient_filters_status_id_fk" => $zeile["ingredient_filters_status_id_fk"]);
		
		array_push($myArray, $array);
		$int++;
	}
	
	$myArticleObj->results = (object) $myArray;
	
	echo json_encode($myArticleObj, JSON_PRETTY_PRINT);
	
}else if(isset($_GET["filter_id"]) && !empty($_GET["filter_id"]) 
		 && isset($_GET["filter_name"]) && !empty($_GET["filter_name"])
		 && isset($_GET["user_id"]) && !empty($_GET["user_id"])
		 && isset($_GET["status_id"]) && !empty($_GET["status_id"])){
	
	//UPDATE
	
	$filter_id = intval($_GET["filter_id"]);
	$filter_name = strval($_GET["filter_name"]);
	$user_id = strval($_GET["user_id"]);
	$status_id = strval($_GET["status_id"]);
	
	$myArticleObj = new stdClass();
	
	$sSQL = "UPDATE bc_ingredient_filters SET ingredient_filters_name=?"
		   . ", user_id_fk=?, ingredient_filters_status_id_fk=? "
	   	   . "WHERE ingredient_filters_id=?";
	
	$stmt = $mysqli->prepare($sSQL);
	$stmt->bind_param("ssii", $filter_name, $user_id, $status_id, $filter_id);
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
	
	$sSQL = "DELETE FROM bc_ingredient_filters WHERE user_id_fk=? AND ingredient_filters_id IN (?)";
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