<?php
include("../include/config.php");
$mysqli = new db();
include("../include/functions.php");

	// Settings
	$sSQL = "SELECT set_bezeichnung, set_wert "
		  . "FROM bc_settings WHERE set_kategory='bc-settings'";
	$resultSettings = $mysqli->query($sSQL);
	$GLOBALS['settings'] = sql2Array($resultSettings);
/*
	


*/

if(isset($_POST["user"]) && isset($_POST["article"]) && isset($_POST["list"]) && !isset($_POST["move"]) && !isset($_POST["bought"])){
	
	/*
		Löscht den Artikel
	*/
	
	$user = strval($_POST["user"]);
	$article = intval($_POST["article"]);
	$list = intval($_POST["list"]);

	/*$sSQL = "DELETE FROM bc_shopping_list "
		  . "WHERE shopping_list_id=" . $article
		  . " AND user_id_fk='" . $user . "'";*/
	
	$sSQL = "DELETE FROM bc_shopping_list "
		  . "WHERE shopping_list_id=?"
		  . " AND user_id_fk=?";

	//echo $sSQL;
	//$result = $mysqli->query($sSQL);
	$stmt = $mysqli->prepare($sSQL);
	$stmt->bind_param("is", $article, $user);
	$stmt->execute();
	
	if($stmt == true){
		echo "success";
	}else{
		echo "warning";
	}
	
	$result = getCartContent($mysqli, $user, $list);
	
	//getShoppingList($result);
	

	
	
}else if(isset($_POST["user"]) && isset($_POST["article"]) && isset($_POST["list"]) && isset($_POST["move"]) && !isset($_POST["bought"])){
	
	
	/*
	Verschiebt den Artikel in die Einkaufsliste oder in den
	Einkaufswagen
	*/
	
	//echo "Dreck";
	
	$user = strval($_POST["user"]);
	$article = intval($_POST["article"]);
	$list = intval($_POST["list"]);
	
	
	
	/*if($_POST["move"] == true){
		
	}else{
		
	}*/
	
	if($_POST["move"] == true){
		$move = 2;
	}else{
		$move = 1;
	}
	
	/*$sSQL = "UPDATE bc_shopping_list "
		  . "SET place_id_fk=" . $move
		  . " WHERE user_id_fk='" . $user . "'"
		  . " AND shopping_lists_id_fk=" . $list
		  . " AND shopping_list_id=" . $article;*/
	
	$sSQL = "UPDATE bc_shopping_list "
		  . "SET place_id_fk=?"
		  . " WHERE user_id_fk=?"
		  . " AND shopping_lists_id_fk=?"
		  . " AND shopping_list_id=?";
	
	//echo $sSQL;
	//$result = $mysqli->query($sSQL);
	$stmt = $mysqli->prepare($sSQL);
	$stmt->bind_param("isii", $move, $user, $list, $article);
	$stmt->execute();
	
	$result = getCartContent($mysqli, $user, $list);

	if($result == true){
		echo "success";
	}else{
		echo "warning";
	}
	
	
}else if(isset($_POST["user"]) && isset($_POST["article"]) && isset($_POST["list"]) && isset($_POST["bought"])){

	/*
		Wird ausgeführt wenn EIN Artikel im Einkaufswagen ist
		und dieser als gekauft deklariert werden soll
	*/
	
	$user = strval($_POST["user"]);
	$article = intval($_POST["article"]);
	$list = intval($_POST["list"]);
	
	if($_POST["bought"] == true){
		$bought = 1;
	}else{
		$bought = 2;
	}
	
	
	/*$sSQL = "UPDATE bc_shopping_list "
		  . "SET shopping_list_bought=" . $bought
		  . ", place_id_fk=3"
		  . " WHERE user_id_fk='" . $user . "'"
		  . " AND shopping_lists_id_fk=" . $list
		  . " AND shopping_list_id=" . $article;*/
	
	$sSQL = "UPDATE bc_shopping_list "
		  . "SET shopping_list_bought=?"
		  . ", place_id_fk=3"
		  . " WHERE user_id_fk=?"
		  . " AND shopping_lists_id_fk=?"
		  . " AND shopping_list_id=?";
	
	//echo $sSQL;
	//$result = $mysqli->query($sSQL);
	$stmt = $mysqli->prepare($sSQL);
	$stmt->bind_param("isii", $bought, $user, $list, $article);
	$stmt->execute();

	if($stmt == true){
		echo "success";
		isListDone($mysqli, $user, $list);
	}else{
		echo "warning";
	}
	
	
}else if(isset($_POST["checkout"]) && isset($_POST["bought"])){
	
	/*
		Wird ausgeführt wenn MEHRERE Artikel im Einkaufswagen sind
		und diese als gekauft deklariert werden sollen
	*/
	
	
	$checkout_article = json_decode($_POST["checkout"]);
	$checkout_result = "warning";
	//print_r($checkout_article);
	
	foreach ($checkout_article as $value){
		
		//print_r($value);
		//echo $checkout_article[$i]["us"]
		
		/*$sSQL = "UPDATE bc_shopping_list "
			  . "SET shopping_list_bought=1, place_id_fk=3"
			  . " WHERE user_id_fk='" . $value->user_id . "'"
			  . " AND shopping_lists_id_fk=" . $value->list
			  . " AND shopping_list_id=" . $value->article_id;*/
		
		$sSQL = "UPDATE bc_shopping_list "
			  . "SET shopping_list_bought=1, place_id_fk=3"
			  . " WHERE user_id_fk=?"
			  . " AND shopping_lists_id_fk=?"
			  . " AND shopping_list_id=?";
		
		
		//$result = $mysqli->query($sSQL);
		$stmt = $mysqli->prepare($sSQL);
		$stmt->bind_param("sii", $value->user_id, $value->list, $value->article_id);
		$stmt->execute();
		
		if($stmt == true){
			$checkout_result = "success";
			isListDone($mysqli, $value->user_id, $value->list);
		}else{
			$checkout_result = "warning";
		}
		
	}
	
	echo $checkout_result;
	
}else if(isset($_POST["user"]) && isset($_POST["list_id"])){
	
		$user = strval($_POST["user"]);
		$list = intval($_POST["list_id"]);
	
		/*$sSQL = "UPDATE bc_shopping_list "
			  . "SET place_id_fk=4"
			  . " WHERE user_id_fk='" . $user . "'"
			  . " AND shopping_list_id=" . $list;*/
	
		$sSQL = "UPDATE bc_shopping_list "
			  . "SET place_id_fk=4"
			  . " WHERE user_id_fk=?"
			  . " AND shopping_list_id=?";
		
		
		//$result = $mysqli->query($sSQL);
		$stmt = $mysqli->prepare($sSQL);
		$stmt->bind_param("si", $user, $list);
		$stmt->execute();
		
		if($stmt == true){
			echo "success";
			//isListDone($mysqli, $value->user_id, $value->list);
		}else{
			echo "warning";
		}
	
}else if(isset($_POST["shopping_id"]) && isset($_POST["quantity"]) && isset($_POST["list"])){
	$shopping_list_id = intval($_POST["shopping_id"]);
	$shopping_list_quantity = intval($_POST["quantity"]);
	$shopping_lists = intval($_POST["list"]);
	$new_entry = "";

	/*$sSQL = "INSERT INTO bc_shopping_list (shopping_lists_id_fk, user_id_fk, article_user_id_fk, shopping_list_picture_id, article_search, shopping_list_name, shopping_list_quantity, unit_id_fk, shopping_list_durability, shopping_list_bought, place_id_fk) SELECT shopping_lists_id_fk, user_id_fk, article_user_id_fk, shopping_list_picture_id, article_search, shopping_list_name, shopping_list_quantity, unit_id_fk, shopping_list_durability, shopping_list_bought, place_id_fk FROM bc_shopping_list WHERE shopping_list_id=" . $shopping_list_id;*/
	
	$sSQL = "INSERT INTO bc_shopping_list (shopping_lists_id_fk, user_id_fk, article_user_id_fk, shopping_list_picture_id, article_search, shopping_list_name, shopping_list_quantity, unit_id_fk, shopping_list_durability, shopping_list_bought, place_id_fk) SELECT shopping_lists_id_fk, user_id_fk, article_user_id_fk, shopping_list_picture_id, article_search, shopping_list_name, shopping_list_quantity, unit_id_fk, shopping_list_durability, shopping_list_bought, place_id_fk FROM bc_shopping_list WHERE shopping_list_id=?";
	
	//$result = $mysqli->query($sSQL);
	$stmt = $mysqli->prepare($sSQL);
	$stmt->bind_param("i", $shopping_list_id);
	$stmt->execute();
	
	//print_r($stmt);
	
	//echo $sSQL;
	//$new_entry = $mysqli->insert_id;
	//echo $new_entry;
	
	//$new_entry = $stmt->get_result()->insert_id;
	$new_entry = $mysqli->insert_id;
	
	//print_r($new_entry);
	//echo $new_entry;
	
	
	
	//$sSQL = "UPDATE bc_shopping_list SET place_id_fk=4 WHERE shopping_list_id=" . $shopping_list_id;
	$sSQL = "UPDATE bc_shopping_list SET place_id_fk=4 WHERE shopping_list_id=?";
	//$result = $mysqli->query($sSQL);
	$stmt = $mysqli->prepare($sSQL);
	$stmt->bind_param("i", $shopping_list_id);
	$stmt->execute();
	//echo $sSQL;
	
	
	/*$sSQL = "UPDATE bc_shopping_list SET shopping_lists_id_fk=" . $shopping_lists . ", shopping_list_quantity=" . $shopping_list_quantity . ", shopping_list_bought=2, place_id_fk=1 WHERE shopping_list_id=" . $new_entry;*/
	
	$sSQL = "UPDATE bc_shopping_list SET shopping_lists_id_fk=?, shopping_list_quantity=?, shopping_list_bought=2, place_id_fk=1 WHERE shopping_list_id=" . $new_entry;
	//echo $sSQL;
	//$result = $mysqli->query($sSQL);
	$stmt = $mysqli->prepare($sSQL);
	$stmt->bind_param("ii", $shopping_lists, $shopping_list_quantity);
	$stmt->execute();
	//echo $sSQL;
	
	if($stmt == true){
		echo "success";
	}else{
		echo "warning";
	}
	
	
}

	function isListDone($mysqli, $user, $list_id){
		$user = strval($user);
		$list_id = intval($list_id);
		
		/*$sSQL = "SELECT * FROM bc_shopping_list "
			  . "WHERE user_id_fk='" . $user_id . "'"
			  . " AND shopping_lists_id_fk=" . $list_id
			  . " AND shopping_list_bought=1";*/
		
		$sSQL = "SELECT * FROM bc_shopping_list "
			  . "WHERE user_id_fk=?"
			  . " AND shopping_lists_id_fk=?"
			  . " AND shopping_list_bought=2";
		
		//$sSQL = "SELECT * FROM bc_shopping_list";
		
		//echo $sSQL;
		//print_r($mysqli->query($sSQL));
		$stmt = $mysqli->prepare($sSQL);
		
		
		$stmt->bind_param("si", $user, $list_id);
		$stmt->execute();
		//print_r($stmt);
		
		$result = $stmt->get_result();
		
		
		if($result->num_rows == 0){
			/*$sSQL = "UPDATE bc_shopping_lists "
			      . "SET shopping_lists_bought=1 "
				  . "WHERE user_id_fk='" . $user_id . "'"
				  . " AND shopping_lists_id=" . $list_id;*/
			
			//$sSQL = "UPDATE bc_shopping_lists SET shopping_lists_bought=1 WHERE user_id_fk=? AND shopping_lists_id=?";
			$sSQL = "UPDATE bc_shopping_lists SET shopping_lists_bought=1 WHERE user_id_fk=? AND shopping_lists_id=?";
			$stmt = $mysqli->prepare($sSQL);
			
			if($stmt){
				$stmt->bind_param("si", $user, $list_id);
				$stmt->execute();
				$stmt->close();
			}else{
				echo("Statement failed: ". $stmt->error . "<br>");
			}
			
		}
		
	}

	/*function getShoppingList($result){
		
		while($shopping_list = $result->fetch_assoc()){
		?>
			<li id="result-<?php echo $shopping_list["shopping_lists_id"] ?>">
			<span class="hiding" id="user_id"><?php echo $shopping_list["user_id_fk"] ?></span>
			<span class="hiding" id="article_id"><?php echo $shopping_list["shopping_list_id"] ?></span><img src="<?php echo $GLOBALS['settings']['bc-website-url']; ?>img/icon-twitter.png">
			<span class="cd-qty"><?php echo $shopping_list["shopping_list_quantity"] ?>x</span> <?php echo $shopping_list["shopping_list_name"] ?>
			<!--<div class="cd-price">$9.99</div></li>-->

			<a href="" class="cd-item-remove cd-img-replace" style="right: 5em;"><button class="btn btn-xs btn-success pull-right"><span class="glyphicon glyphicon-arrow-right"></span></button></a>

			<a href="<?php echo $GLOBALS['settings']['bc-website-url']; ?>site/login/edit_shopping_list/sublist/<?php echo $shopping_list["shopping_lists_id"] ?>/article/<?php echo $shopping_list["shopping_list_id"] ?>" class="cd-item-remove cd-img-replace" style="right: 3em;"><button class="btn btn-xs btn-primary pull-right"><span class="glyphicon glyphicon-pencil"></span></button></a>

			<a class="cd-item-remove cd-img-replace list-remove" style="right: 1em;"><button class="btn btn-xs btn-danger pull-right"><span class="glyphicon glyphicon-remove"></span></button></a></li>
		<?php
		}
		
	}*/


	// Ermittelt den Inhalt des GANZEN Einkaufswagens
	function getCompleteCart($mysqli, $user){
		$user = strval($user);
		
		/*$sSQL = "SELECT shopping_list_id, bc_shopping_lists.shopping_lists_id, "
			  . "bc_shopping_lists.shopping_lists_name, "
			  . "bc_shopping_lists.user_id_fk, article_user_id_fk, article_search, "
			  . "shopping_list_name, shopping_list_quantity, bc_unit.unit_name, "
			  . "shopping_list_durability, shopping_list_bought "
			  . "FROM bc_shopping_list "
			  . "INNER JOIN bc_shopping_lists "
			  . "ON bc_shopping_list.shopping_lists_id_fk = bc_shopping_lists.shopping_lists_id "
			  . "INNER JOIN bc_unit "
			  . "ON bc_shopping_list.unit_id_fk=bc_unit.unit_id "
			  . "WHERE bc_shopping_list.user_id_fk='" . $user . "'"
			  . " AND shopping_list_bought=2";*/
		
		$sSQL = "SELECT shopping_list_id, bc_shopping_lists.shopping_lists_id, "
			  . "bc_shopping_lists.shopping_lists_name, "
			  . "bc_shopping_lists.user_id_fk, article_user_id_fk, article_search, "
			  . "shopping_list_name, shopping_list_quantity, bc_unit.unit_name, "
			  . "shopping_list_durability, shopping_list_bought "
			  . "FROM bc_shopping_list "
			  . "INNER JOIN bc_shopping_lists "
			  . "ON bc_shopping_list.shopping_lists_id_fk = bc_shopping_lists.shopping_lists_id "
			  . "INNER JOIN bc_unit "
			  . "ON bc_shopping_list.unit_id_fk=bc_unit.unit_id "
			  . "WHERE bc_shopping_list.user_id_fk=?"
			  . " AND shopping_list_bought=2";
		
		//echo $sSQL;
				  
		//$result = $mysqli->query($sSQL);
		$stmt = $mysqli->prepare($sSQL);
		$stmt->bind_param("s", $user);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		return $result;
				
	}


	// Ermittelt den Inhalt der (EINKAUFSLISTE)
	function getCartContent($mysqli, $user, $list_id){
		$user = strval($user);
		$list_id = intval($list_id);
		
		/*$sSQL = "SELECT shopping_list_id, bc_shopping_lists.shopping_lists_id, "
			  . "bc_shopping_lists.shopping_lists_name, "
			  . "bc_shopping_lists.user_id_fk, article_user_id_fk, article_search, "
			  . "shopping_list_name, shopping_list_quantity, bc_unit.unit_name, "
			  . "shopping_list_durability, shopping_list_bought "
			  . "FROM bc_shopping_list "
			  . "INNER JOIN bc_shopping_lists "
			  . "ON bc_shopping_list.shopping_lists_id_fk = bc_shopping_lists.shopping_lists_id "
			  . "INNER JOIN bc_unit "
			  . "ON bc_shopping_list.unit_id_fk=bc_unit.unit_id "
			  . "WHERE shopping_list_bought=2"
			  . " AND bc_shopping_list.user_id_fk='" . $user . "'"
			  . " AND shopping_lists_id=" . $list_id
			  . " AND place_id_fk=1";*/
		
		
		$sSQL = "SELECT shopping_list_id, bc_shopping_lists.shopping_lists_id, "
			  . "bc_shopping_lists.shopping_lists_name, "
			  . "bc_shopping_lists.user_id_fk, article_user_id_fk, article_search, "
			  . "shopping_list_name, shopping_list_quantity, bc_unit.unit_name, "
			  . "shopping_list_durability, shopping_list_bought "
			  . "FROM bc_shopping_list "
			  . "INNER JOIN bc_shopping_lists "
			  . "ON bc_shopping_list.shopping_lists_id_fk = bc_shopping_lists.shopping_lists_id "
			  . "INNER JOIN bc_unit "
			  . "ON bc_shopping_list.unit_id_fk=bc_unit.unit_id "
			  . "WHERE shopping_list_bought=2"
			  . " AND bc_shopping_list.user_id_fk=?"
			  . " AND shopping_lists_id=?"
			  . " AND place_id_fk=1";
		
		//echo $sSQL;
				  
		//$result = $mysqli->query($sSQL);
		$stmt = $mysqli->prepare($sSQL);
		$stmt->bind_param("si", $user, $list_id);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		return $result;
				
	}


?>