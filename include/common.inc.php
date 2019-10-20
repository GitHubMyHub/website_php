<?php


	if(empty($sWhere)){
		$sWhere = "";
	}

	//echo $_GET["list"];
	//echo $_GET["id"];

	//echo "SHWERE:" . $sWhere . "<br />";

	if(!empty($_GET["article_id"])){
		$article_id = intval($_GET["article_id"]);
		$sWhere = " WHERE article_id_fk=" . $article_id;
	}

	if(!empty($_GET["company_id"])){
		$company_id = intval($_GET["company_id"]);
		$sWhere = " WHERE company_id_fk=" . $company_id;
	}

	if(!empty($_GET["nutrition_quantity_id"])){
		$nutrition_quantity_id = intval($_GET["nutrition_quantity_id"]);
		$sWhere = " WHERE nutrition_quantity_id=" . $nutrition_quantity_id;
	}

	if(!empty($_GET["property_type_id"])){
		$property_type_id = intval($_GET["property_type_id"]);
		$sWhere = " WHERE properties_rel_id_fk=" . $property_type_id;
	}

	if(!empty($_GET["properties_rel_id"])){
		$properties_rel_id = intval($_GET["properties_rel_id"]);
		$sWhere = " WHERE properties_rel_id_fk=" . $properties_rel_id;
	}

	if(!empty($_GET["storage_rel_id"])){
		$storage_rel_id = intval($_GET["storage_rel_id"]);
		$sWhere = " WHERE storage_rel_id_fk=" . $storage_rel_id;
	}

	// legt die userspezifischen
	// Datenbankeinträge fest
	if(isset($sAuthSite) and array_search($sAuthSite, $oAuth->getAuthSites()) !== false){
		if($oAuth->getLoginOk()){
			$sWhere = $sWhere . " WHERE user_id_fk ='" . $oAuth->getSessionToId() . "'";
		}
	}

	//echo $_GET["list"];

	if(!empty($_GET["sublist"])){
		$shopping_lists_id = intval($_GET["sublist"]);
		$sWhere = $sWhere . " AND shopping_lists_id_fk=" . $shopping_lists_id;
	}

	if(!empty($_GET["view"]) && $_GET["view"] == "list_shopping_lists"){
		$sWhere = $sWhere . " AND shopping_lists_bought=2 ";
	}

	if(!empty($_GET["subfilter"])){
		$ingredient_filters_id = intval($_GET["subfilter"]);
		$sWhere = $sWhere . " AND ingredient_filters_id_fk=" . $ingredient_filters_id;
	}

	// Main SQL Query
	if($sSQL == "" or preg_match("/bc_settings/", $sSQL)){
		$sSQL = "SELECT * FROM " . $sSource . $sWhere;
		//echo $sSQL;
	}

	//echo "Main: " . $sSQL . "<br />";
	$resultFields = $mysqli->query($sSQL);
	$userParam = "";

	// Wenn Admin dann soll auch user unabhängige
	// Einträge gelöscht werden.
	/*if($oAuth->getAdminAccess()){
		echo "Admin: JA";
	}else{
		echo "Admin: NEIN";
	}*/
	if($oAuth->getAdminAccess() != true){
		$userParam = " AND user_id_fk='" . $oAuth->getSessionToId() . "'";
	}

	// delete aller foreign keys in der Shopping_lists
	if(isset($_GET["view"]) && $_GET["view"] == "list_shopping_lists" && empty($_GET["sublist"]) && isset($_GET["action"]) && $_GET["action"] == "delete"){
		
		deleteUpload($mysqli);
		
		$sSQLDelete = "DELETE FROM bc_shopping_list WHERE " . preg_replace("/bc_/", "", $sSource) . "_id_fk=" . intval($_GET["id"]) . $userParam;
		//echo $sSQLDelete;
		$result = $mysqli->query($sSQLDelete);
		
		?><div class="alert alert-success"><?php echo $lang[$_SESSION['speak']]["Delete Shoppinglist"]; ?></div><?php
	}

	// delete aller foreign keys in der filter_liste
	if(isset($_GET["view"]) && $_GET["view"] == "list_ingredient_filters" && empty($_GET["subfilter"]) && isset($_GET["action"]) && $_GET["action"] == "delete"){
		$sSQLDelete = "DELETE FROM bc_ingredient_filter WHERE " . preg_replace("/bc_/", "", $sSource) . "_id_fk=" . intval($_GET["id"]) . $userParam;
		echo $sSQLDelete;
		$result = $mysqli->query($sSQLDelete);
		?><div class="alert alert-success"><?php echo $lang[$_SESSION['speak']]["Delete Filterlist"]; ?></div><?php
	}
	
	// delete aller foreign keys in der filter_liste
	if(isset($_GET["view"]) && $_GET["view"] == "list_properties_rel" && isset($_GET["action"]) && $_GET["action"] == "delete"){
		$sSQLDelete = "DELETE FROM bc_released_rel WHERE " . preg_replace("/bc_/", "", $sSource) . "_id_fk=" . intval($_GET["id"]) . $userParam;
		echo $sSQLDelete;
		$result = $mysqli->query($sSQLDelete);
		?><div class="alert alert-success"><?php echo $lang[$_SESSION['speak']]["Delete Property-type"]; ?></div><?php
	}

	// delete eines Eintrags
	if(isset($_GET["action"]) && $_GET["action"] == "delete"){
		$sSQLDelete = "DELETE FROM " . $sSource . " WHERE " . preg_replace("/bc_/", "", $sSource) . "_id=" . intval($_GET["id"]) . $userParam;
		echo $sSQLDelete;
		
		if($_GET["view"] == "list_shopping_list"){
			deleteUpload($mysqli);
		}
		
		
		$result = $mysqli->query($sSQLDelete);
	?><div class="alert alert-success"><?php echo $lang[$_SESSION['speak']]["Delete Entry"]; ?></div><?php
	}

	function deleteUpload($mysqli){
		$sSQL = "SELECT bc_shopping_lists.user_id_fk, shopping_list_id 
		         FROM bc_shopping_lists 
				 INNER JOIN bc_shopping_list 
				 ON bc_shopping_lists.shopping_lists_id=bc_shopping_list.shopping_lists_id_fk
				 WHERE bc_shopping_list.shopping_lists_id_fk=" . intval($_GET["id"]);
		$result = $mysqli->query($sSQL);
		
		$path = "";
		while($zeile = $result->fetch_assoc()){
			//echo "COOL";
			$path = "uploads/article_upload/user_id_" . $zeile["user_id_fk"] . "/shopping_list_id_" . $zeile["shopping_list_id"];
			deleteFile($path);
		}	
	}

	// Set Page
	if(empty($_GET["page"])){
		$page = 1;
	}else {
		$page = intval($_GET["page"]);
		if($page == 0){
			$page = 1;
		}
	}

	
	$file_edit = "edit_" . preg_replace("/bc_/", "", $sSource);
	$file_list = "list_" . preg_replace("/bc_/", "", $sSource);	

	$gesamt = howmany($mysqli, $sSQL, '');
	$seitengesamt = ceil($gesamt / $settings[getEntryPerPage(preg_replace("/bc_/", "", $sSource))]);
	$seitengesamt2 = $settings[getEntryPerPage(preg_replace("/bc_/", "", $sSource))];
	$offset = ($page - 1) * $settings[getEntryPerPage(preg_replace("/bc_/", "", $sSource))];
	
	
	
	/*echo "Gesamt:" . $gesamt . "<br />";
	echo "Offset:" . $offset . "<br />";
	echo "Seitengesamt:" . $seitengesamt . "<br />";
	echo "Seitengesamt2:" . $seitengesamt2 . "<br />";*/
	
	// Second SQL Query
	//
	$sSQL = "SELECT * FROM " . $sSource . $sWhere . " ORDER BY " . preg_replace("/bc_/", "", $sSource) . "_id DESC LIMIT " . $offset . "," . $seitengesamt2;
	//echo "Second: " . $sSQL;
	$resultFields = $mysqli->query($sSQL);
	
?>