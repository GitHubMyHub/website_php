<?php
include("../include/config.php");
$mysqli = new db();
/*
	Dieses PHP Skript ist für das hinzufügen der
	Artikel zu einer Artikelliste oder der 
	Artikel-Favoriten-Liste


*/

	
if(isset($_POST["user"]) == true && isset($_POST["article"]) == true 
   								 && isset($_POST["count"]) == true 
   								 && isset($_POST["list"]) == true){
	
	$list = intval($_POST["list"]);
	$user = strval($_POST["user"]);
	$article = intval($_POST["article"]);
	$count = intval($_POST["count"]);
	
	$paramsA = [$list, $user, $article];
	
	
	
	/*$sSQL = "SELECT * FROM bc_article "
		  . "WHERE article_id=" . $article;*/
	
	$sSQL = "SELECT * FROM bc_article "
		  . "WHERE article_id=?";
	
	//echo $sSQL;
	//$result = $mysqli->query($sSQL);
	$stmt = $mysqli->prepare($sSQL);
	$stmt->bind_param("i", $article);
	$stmt->execute();
	
	//$stmt->store_result();
	
	
	

    $res = $stmt->get_result();
	
	//var_dump($res);
	
	$zeile = $res->fetch_assoc();
	
	/*$sSQL = "SELECT * FROM bc_shopping_list "
		  . "WHERE shopping_lists_id_fk= " . $list 
		  . " AND user_id_fk='" . $user . "'"
		  . " AND article_user_id_fk=" . $article 
		  . " AND shopping_list_name='" . $zeile["article_name"] . "'"
		  . " AND shopping_list_bought=2";*/
		  
	
	$sSQL = "SELECT * FROM bc_shopping_list "
		  . "WHERE shopping_lists_id_fk=? " 
		  . " AND user_id_fk=? "
		  . " AND article_user_id_fk=? "
		  . " AND shopping_list_name=? "
		  . " AND shopping_list_bought=2";
	
	$paramsAdd = array();
	$paramsAdd = $paramsA;
	array_push($paramsAdd, $zeile["article_name"]);
	
	$stmt = $mysqli->prepare($sSQL);
	$stmt->bind_param("iiis", ...$paramsAdd);
	$stmt->execute();
	
	//$stmt->store_result();
	
	$result = $stmt->get_result();
	
	//print_r($result);
	
	//$result2 = $mysqli->query($sSQL);
	//$zeile2 = $result2->fetch_assoc();
		  
	if($result->num_rows >= 1){
		
		$paramsAdd3 = [$count, $list, $user, $article];
		//print_r($paramsAdd3);
		
		// Wenn Eintrag vorhanden dann Update 
		/*$sSQL = "UPDATE bc_shopping_list "
			  . "SET shopping_list_quantity = shopping_list_quantity + " . $count
			  . " WHERE shopping_lists_id_fk=" . $list
			  . " AND user_id_fk='" . $user . "'"
			  . " AND article_user_id_fk=" . $article . " LIMIT 1";*/
		
		$sSQL = "UPDATE bc_shopping_list "
			  . "SET shopping_list_quantity = shopping_list_quantity + ? "
			  . " WHERE shopping_lists_id_fk=? "
			  . " AND user_id_fk=? "
			  . " AND article_user_id_fk=? LIMIT 1";
		
		//echo $sSQL;
		$stmt = $mysqli->prepare($sSQL);
		//print_r($stmt);
		$stmt->bind_param("iisi", ...$paramsAdd3);
		
	}else{
		
		$paramsAdd2 = array();
		$paramsAdd2 = $paramsAdd;
		
		array_push($paramsAdd2, $count);
		
		// Wenn Eintrag in der Shopping-List nicht vorhanden dann neuen anlegen
		//ACHTUNG WENN BARCODE_NUMMER BENUTZT WIRD DANN AUCH DIE EINHEIT "unit_id_fk" festlegen
		/*$sSQL = "INSERT INTO bc_shopping_list (shopping_lists_id_fk, user_id_fk, "
			  . "article_user_id_fk, article_search, shopping_list_name, "
			  . "shopping_list_quantity, unit_id_fk, shopping_list_durability, shopping_list_bought) "
			  . "VALUES (" . $list .  ", '" . $user . "', " . $article . ", NULL, '" . $zeile["article_name"] . "', " . $count . ", 1, 'NULL', 2)";*/
		
		$sSQL = "INSERT INTO bc_shopping_list (shopping_lists_id_fk, user_id_fk, "
			  . "article_user_id_fk, article_search, shopping_list_name, "
			  . "shopping_list_quantity, unit_id_fk, shopping_list_durability, shopping_list_bought) "
			  . "VALUES (?, ?, ?, NULL, ?, ?, 1, 'NULL', 2)";
	
		$stmt = $mysqli->prepare($sSQL);
		$stmt->bind_param("isisi", ...$paramsAdd2);
	}
	
	
	$stmt->execute();
	
	//echo $sSQL;
	//$result = $mysqli->query($sSQL);
	
	
	
	if($stmt){
		echo "success";
	}else{
		echo "warning";
	}
	//echo $user . ":" . $article . ":" . $count . ":" . $list;
	
	
	
}else if(isset($_POST["user"]) == true && isset($_POST["article"]) == true){
	
	$params = [$_POST["article"], $_POST["user"]];
	
	$sSQL = "SELECT * FROM bc_user_favorites "
		  . "WHERE article_user_id_fk=? "
		  . " AND user_id_fk=?";
	
	//echo $sSQL;
		
	//$result = $mysqli->query($sSQL);
	$stmt = $mysqli->prepare($sSQL);
	$stmt->bind_param("is", ...$params);
	$stmt->execute();
	
	if($stmt->num_rows == 0){
		
		$sSQL = "INSERT INTO bc_user_favorites "
			  . "(article_user_id_fk, user_id_fk) "
			  . "VALUES (?, ?)";
		$stmt->prepare($sSQL);
		$stmt->bind_param("is", ...$params);
		$stmt->execute();
	}	
	
	if($stmt == true){
		echo "success";
	}else{
		echo "warning";
	}
	
	//echo $user . ":" . $article;
	
}

?>