<?php

	include("../include/config.php");
	$mysqli = new db();
	$filter_found = array();
	$vegan_found = false;
	$vegie_found = false;

	if(isset($_GET["user_id"]) && !empty($_GET["user_id"]) && isset($_GET["article_id"]) && !empty($_GET["article_id"])){
		
		$user_id = strval($_GET["user_id"]);
		$article_id = intval($_GET["article_id"]);
		
		// COMPONENTS
		/*$sSQL = "SELECT a.filters_id_fk, (SELECT GROUP_CONCAT(components_id_fk SEPARATOR ',') as components_id_fk "
			  . "FROM bc_filters_rel b "
			  . "INNER JOIN bc_components_filters d "
			  . "ON b.filters_id_fk=d.filters_id_fk "
			  . "WHERE user_id_fk='" . $user_id . "' AND b.filters_id_fk=a.filters_id_fk) as components_id_fk "
			  . "FROM bc_filters_rel a "
			  . "WHERE user_id_fk='" . $user_id . "'"
			  . "GROUP BY filters_id_fk, components_id_fk";*/
		
		
		$sSQL = "SELECT a.filters_id_fk, (SELECT GROUP_CONCAT(components_id_fk SEPARATOR ',') as components_id_fk "
			  . "FROM bc_filters_rel b "
			  . "INNER JOIN bc_components_filters d "
			  . "ON b.filters_id_fk=d.filters_id_fk "
			  . "WHERE user_id_fk=? AND b.filters_id_fk=a.filters_id_fk) as components_id_fk "
			  . "FROM bc_filters_rel a "
			  . "WHERE user_id_fk=? "
			  . "GROUP BY filters_id_fk, components_id_fk";
		
		//echo $sSQL;
		//$result_filters = $mysqli->query($sSQL);
		$stmt = $mysqli->prepare($sSQL);
		$stmt->bind_param("ss", $user_id, $user_id);
		$stmt->execute();
		
		$result_filters = $stmt->get_result();
		
		while($zeile_filters = $result_filters->fetch_assoc()){
		
			if(!empty($zeile_filters["components_id_fk"])){
				/*$sSQL = "SELECT components_id_fk FROM bc_ingredients_rel WHERE article_id_fk=" . $article_id . " AND components_id_fk IN (" . $zeile_filters["components_id_fk"] . ")";*/
				
				
				$sSQL = "SELECT components_id_fk FROM bc_ingredients_rel WHERE article_id_fk=? AND components_id_fk IN (?)";
				
				//echo $sSQL . "<br />";
				//$result_components = $mysqli->query($sSQL);
				$stmt = $mysqli->prepare($sSQL);
				$stmt->bind_param("is", $article_id, $zeile_filters["components_id_fk"]);
				$stmt->execute();
				
				$result_components = $stmt->get_result();

				$zeile_components = $result_components->fetch_assoc();
				if(!empty($zeile_components["components_id_fk"])){

					//$sSQL = "SELECT filters_id, filters_name FROM bc_filters WHERE filters_id=" . $zeile_filters["filters_id_fk"];
					$sSQL = "SELECT filters_id, filters_name FROM bc_filters WHERE filters_id=?";
					//echo $sSQL . "<br />";
					//$result = $mysqli->query($sSQL);
					$stmt = $mysqli->prepare($sSQL);
					$stmt->bind_param("i", $zeile_filters["filters_id_fk"]);
					$stmt->execute();
					
					$result = $stmt->get_result();

					while($zeile_filter = $result->fetch_assoc()){
						//$filter_found[$zeile_filter["filters_id"]] = $zeile_filter["filters_name"];
						array_push($filter_found, $zeile_filter["filters_name"]);
						//echo $zeile_filter["filters_name"];
					}
					//echo json_encode($filter_found);
				}
			}
		}
		
		
		// Prüfung ob Vegie oder Vegan einer Zutat in bc_properties_rel
		/*$sSQL = "SELECT filters_id_fk FROM bc_filters_rel "
			  . "WHERE user_id_fk='" . $user_id . "' "
			  . "AND (filters_id_fk=4 or filters_id_fk=6)";*/
		
		$sSQL = "SELECT filters_id_fk FROM bc_filters_rel "
			  . "WHERE user_id_fk=? "
			  . "AND (filters_id_fk=4 or filters_id_fk=6)";
		
		//$result_filters = $mysqli->query($sSQL);
		
		$stmt = $mysqli->prepare($sSQL);
		$stmt->bind_param("s", $user_id);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		if($result_filters->num_rows > 0){
	
				while($zeile_filters = $result_filters->fetch_assoc()){
					// 4=Vegan
					// 6=Vegie
					if($zeile_filters["filters_id_fk"] == 4){
						/*$sSQL = "SELECT bc_ingredients_rel.components_id_fk, "
						  . "bc_property_type.property_type_vegie, bc_property_type.property_type_vegan "
						  . "FROM bc_ingredients_rel "
						  . "LEFT JOIN bc_properties_rel "
						  . "ON bc_ingredients_rel.components_id_fk=bc_properties_rel.components_id_fk "
						  . "LEFT JOIN bc_property_type "
						  . "ON bc_properties_rel.property_type_id_fk= bc_property_type.property_type_id "
						  . "WHERE article_id_fk=" . $article_id . " AND property_type_vegan=2";*/
						
						$sSQL = "SELECT bc_ingredients_rel.components_id_fk, "
						  . "bc_property_type.property_type_vegie, bc_property_type.property_type_vegan "
						  . "FROM bc_ingredients_rel "
						  . "LEFT JOIN bc_properties_rel "
						  . "ON bc_ingredients_rel.components_id_fk=bc_properties_rel.components_id_fk "
						  . "LEFT JOIN bc_property_type "
						  . "ON bc_properties_rel.property_type_id_fk= bc_property_type.property_type_id "
						  . "WHERE article_id_fk=? AND property_type_vegan=2";
						
						
						//echo $sSQL;
						
						
						//$result_components = $mysqli->query($sSQL);
						$stmt = $mysqli->prepare($sSQL);
						$stmt->bind_param("i", $article_id);
						$stmt->execute();
						
						$result_components = $stmt->get_result();
						
						if($result_components->num_rows > 0){
							$vegan_found = true;
						}
						
						
					}else if($zeile_filters["filters_id_fk"] == 6){
						/*$sSQL = "SELECT bc_ingredients_rel.components_id_fk, "
						  . "bc_property_type.property_type_vegie, bc_property_type.property_type_vegan "
						  . "FROM bc_ingredients_rel "
						  . "LEFT JOIN bc_properties_rel "
						  . "ON bc_ingredients_rel.components_id_fk=bc_properties_rel.components_id_fk "
						  . "LEFT JOIN bc_property_type "
						  . "ON bc_properties_rel.property_type_id_fk= bc_property_type.property_type_id "
						  . "WHERE article_id_fk=" . $article_id . " AND property_type_vegie=2";*/
						
						
						$sSQL = "SELECT bc_ingredients_rel.components_id_fk, "
						  . "bc_property_type.property_type_vegie, bc_property_type.property_type_vegan "
						  . "FROM bc_ingredients_rel "
						  . "LEFT JOIN bc_properties_rel "
						  . "ON bc_ingredients_rel.components_id_fk=bc_properties_rel.components_id_fk "
						  . "LEFT JOIN bc_property_type "
						  . "ON bc_properties_rel.property_type_id_fk= bc_property_type.property_type_id "
						  . "WHERE article_id_fk=? AND property_type_vegie=2";
						
						//echo $sSQL;
						//$result_components = $mysqli->query($sSQL);
						$stmt = $mysqli->prepare($sSQL);
						$stmt->bind_param("i", $article_id);
						$stmt->execute();
						
						$result_components = $stmt->get_result();
						
						if($result_components->num_rows > 0){
							$vegie_found = true;
						}
					}

				}
			
				if($vegan_found == true || $vegie_found == true){
					/*$sSQL = "SELECT filters_name FROM bc_filters "
						  . "WHERE filters_id=4 or filters_id=6";*/
					
					$sSQL = "SELECT filters_name FROM bc_filters "
						  . "WHERE filters_id=4 or filters_id=6";
					
					//echo $sSQL;
					$stmt = $mysqli->prepare($sSQL);
					$stmt->execute();
					
					$filters = $stmt->get_result();
					
					while($zeileFilter = $filters->fetch_assoc()){
				
						if($vegan_found == true && $zeileFilter["filters_name"] == "Vegan (Vegan laut Zutatenliste)"){
							//echo $zeileFilter["filters_name"];
							array_push($filter_found, $zeileFilter["filters_name"]);
						}else if($vegie_found == true && $zeileFilter["filters_name"] == "Vegetarisch (Vegetarisch laut Zutatenliste)"){
							//echo $zeileFilter["filters_name"];
							array_push($filter_found, $zeileFilter["filters_name"]);
						}
					}
				}
			
		}

		
		// ZERTIFICATE
		/*$sSQL = "SELECT a.filters_id_fk, (SELECT GROUP_CONCAT(zertificate_id_fk SEPARATOR ',') as zertificate_id_fk " 
			  . "FROM bc_filters_rel b "
			  . "INNER JOIN bc_zertificate_filters d ON b.filters_id_fk=d.filters_id_fk "
              . "WHERE user_id_fk='" . $user_id . "' AND b.filters_id_fk=a.filters_id_fk) as zertificate_id_fk "
              . "FROM bc_filters_rel a WHERE user_id_fk='" . $user_id . "' GROUP BY filters_id_fk, zertificate_id_fk";*/
		
		$sSQL = "SELECT a.filters_id_fk, (SELECT GROUP_CONCAT(zertificate_id_fk SEPARATOR ',') as zertificate_id_fk " 
			  . "FROM bc_filters_rel b "
			  . "INNER JOIN bc_zertificate_filters d ON b.filters_id_fk=d.filters_id_fk "
              . "WHERE user_id_fk=? AND b.filters_id_fk=a.filters_id_fk) as zertificate_id_fk "
              . "FROM bc_filters_rel a WHERE user_id_fk=? GROUP BY filters_id_fk, zertificate_id_fk";
		
		//echo $sSQL;
		//$result_filters = $mysqli->query($sSQL);
		$stmt = $mysqli->prepare($sSQL);
		$stmt->bind_param("ss", $user_id, $user_id);
		$stmt->execute();
		
		$result_filters = $stmt->get_result();
		
		while($zeile_filters = $result_filters->fetch_assoc()){
		
			if(!empty($zeile_filters["zertificate_id_fk"])){
				/*$sSQL = "SELECT zertificate_id_fk FROM bc_zertificate_rel WHERE article_id_fk=" . $article_id . " AND zertificate_id_fk IN (" . $zeile_filters["zertificate_id_fk"] . ")";*/
				
				$sSQL = "SELECT zertificate_id_fk FROM bc_zertificate_rel WHERE article_id_fk=? AND zertificate_id_fk IN (?)";
				
				//echo $sSQL . "<br />";
				//$result_components = $mysqli->query($sSQL);
				$stmt = $mysqli->prepare($sSQL);
				$stmt->bind_param("is", $article_id, $zeile_filters["zertificate_id_fk"]);
				$stmt->execute();
				
				$result_components = $stmt->get_result();

				$zeile_components = $result_components->fetch_assoc();
				if(empty($zeile_components["zertificate_id_fk"])){

					//$sSQL = "SELECT filters_id, filters_name FROM bc_filters WHERE filters_id=" . $zeile_filters["filters_id_fk"];
					$sSQL = "SELECT filters_id, filters_name FROM bc_filters WHERE filters_id=?";
					//echo $sSQL . "<br />";
					//$result = $mysqli->query($sSQL);
					$stmt = $mysqli->prepare($sSQL);
					$stmt->bind_param("s", $zeile_filters["filters_id_fk"]);
					$stmt->execute();
					
					$result = $stmt->get_result();

					while($zeile_filter = $result->fetch_assoc()){
						//$filter_found[$zeile_filter["filters_id"]] = $zeile_filter["filters_name"];
						array_push($filter_found, $zeile_filter["filters_name"]);
						//echo $zeile_filter["filters_name"];
					}
					//echo json_encode($filter_found);
				}
			}
		}
		echo json_encode($filter_found);
	}

?>