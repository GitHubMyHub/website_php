<?php

include("../include/config.php");
//include("include/picture-class.php");
$mysqli = new db();

if($_POST and isset($_POST["radio"]))
{
	

	$q = preg_replace("/-/", "", strtolower($mysqli->real_escape_string($_POST['search'])));
	$sSQL = "";
	$sFilter = "";
	$sSQLFilter = "";
	//echo "ERGEBNIS:" . $sSQL . "<br />";
	
	//echo $_POST["radio"];
	
	
	
	if(isset($_POST["filter"]) && !empty($_POST["filter"])){
		$sFilter = $mysqli->real_escape_string($_POST["filter"]);
	} 

	if($_POST["radio"] == "first" or $_POST["radio"] == "second"){
		
		if($sFilter != ""){
			$sSQLSub = "SELECT GROUP_CONCAT(components_id_fk SEPARATOR ', ') as components_id_fk FROM bc_ingredient_filter WHERE ingredient_filters_id_fk IN(" . $sFilter . ")";
			$result = $mysqli->query($sSQLSub);
			$zeile = $result->fetch_assoc();
			$sSQLFilter .= "AND components_id_fk NOT IN (" . $zeile["components_id_fk"] . ") ";
			//echo $sSQLFilter;
		}
		
		$sSQL = "SELECT article_id, article_name, "
			  . "article_description, picture_id, "
			  . "picture_source, picture_praefix, picture_postfix "
			  . "FROM bc_article "
			  . "INNER JOIN bc_picture "
			  . "ON bc_article.picture_id_fk=bc_picture.picture_id "
			  . "INNER JOIN bc_ingredients_rel "
              . "ON bc_article.article_id=bc_ingredients_rel.article_id_fk "
              . "INNER JOIN bc_barcode "
			  . "ON bc_article.article_id = bc_barcode.article_id_fk "
			  . "AND picture_source='article' "
			  . "WHERE article_visible=1 "
			  . "AND article_id "
			  . "NOT IN (SELECT article_id_fk FROM bc_history) "
			  . $sSQLFilter
			  . " AND barcode_code like ? "
			  . "ORDER BY article_id DESC LIMIT 5;";
		
		/*$sSQL = "SELECT article_id, article_name, "
			  . "article_description, picture_id_fk "
			  . "FROM bc_article "
              . "INNER JOIN bc_barcode "
			  . "ON bc_article.article_id = bc_barcode.article_id_fk "
			  . "WHERE "
			  . "barcode_code like ? "
			  . "ORDER BY article_id DESC LIMIT 5;";*/
		

		
	}else if($_POST["radio"] == "third"){
		/*$sSQL = "SELECT article_id, "
			  . "article_name, article_description "
			  . "FROM bc_article "
			  . "WHERE lower(article_name) like '%$q%' "
			  . "or lower(article_description) like '%$q%' "
			  . "order by article_id DESC LIMIT 5";*/
		$sSQL = "SELECT article_id, article_name, "
			  . "article_description, picture_id, "
			  . "picture_source, picture_praefix, picture_postfix  "
			  . "FROM bc_article "
			  . "INNER JOIN bc_picture ON "
			  . "bc_article.picture_id_fk=bc_picture.picture_id "
			  . "INNER JOIN bc_ingredients_rel "
              . "ON bc_article.article_id=bc_ingredients_rel.article_id_fk "
			  . "AND picture_source='article' "
			  . "WHERE article_visible=1 "
			  . "AND article_id "
			  . "NOT IN (SELECT article_id_fk FROM bc_history) ";

		if($sFilter != ""){
			$sSQLSub = "SELECT GROUP_CONCAT(components_id_fk SEPARATOR ', ') as components_id_fk FROM bc_ingredient_filter WHERE ingredient_filters_id_fk IN(" . $sFilter . ")";
			//echo $sSQLSub;
			$result = $mysqli->query($sSQLSub);
			$zeile = $result->fetch_assoc();
			$sSQL .= "AND components_id_fk NOT IN (" . $zeile["components_id_fk"] . ") ";

		}
			  $sSQL .= "AND (lower(article_name) like ? 
			  OR lower(article_description) like ? )
			  ORDER BY article_id DESC LIMIT 5";
	}

	//echo $sSQL . "<br />";
	
	$articleResult = __prepareStmtArticle($mysqli, $sSQL, $_POST["radio"], $q);
	

	
	//$articleResult = prepareStmtArticle($mysqli, $sSQL, $q) or die(mysql_error());
	
	//$articleResult = $mysqli->query($sSQL) or die(mysql_error());
	
	while($zeile = $articleResult->fetch_assoc()){
		$article_id			= $zeile["article_id"];
		$articlename		= $zeile["article_name"];
		$articledesc		= strip_tags($zeile["article_description"]);
        $b_articlename 		= '<strong>'.$q.'</strong>';
        $b_articledesc    	= '<strong>'.$q.'</strong>';
        $final_articlename 	= str_ireplace($q, $b_articlename, $articlename);
        $final_articledesc  = str_ireplace($q, $b_articledesc, $articledesc);
		
		/*$oPicture = new Picture($mysqli);
		echo "Picture_id_fk " . $zeile["picture_id_fk"] . "<br />";
		$oPicture->setArticlePictures($zeile["picture_id_fk"], "bc_article");
		echo "Name " . $zeile["article_name"];
		$oPicture->setArticle_name($zeile["article_name"]);
		$oPicture->setPath("http://localhost/bootstrap/");
		$oPicture->getPictures();
		echo $oPicture->getFullPath();*/
		
		//echo $zeile["picture_praefix"] . $zeile["article_names"] . $zeile["picture_postfix"];
		
			// Image-name
			$imageInfo = __getImageInfo($zeile["picture_praefix"], 
						$zeile["article_name"], 
						$zeile["picture_postfix"]);
		
		?>
            <a class="dropdown-articles" href="http://192.168.178.41/site/list_articles/article/<?php echo $article_id; ?>"><div class="show" align="left">
                <img src="http://192.168.178.41/uploads/article/<?php echo $zeile["picture_id"] . "/" . $imageInfo; ?>_thumbnail.png" style="width:50px; height:50px; float:left; margin-right:6px;" /><span class="name"><?php echo $final_articlename; ?></span>&nbsp;<br/><span class="description"><?php echo $final_articledesc; ?></span><br/>
            </div></a>
		
		<?php
	}
}

//
// Methode : Funktion für die Erstellung eines prepared Statements
//         : an die Artikel-Datenbank.
function __prepareStmtArticle($mysqli, $sSQL, $sType, $sParam) {
		//echo "KAKA:" . $sParam;
		$resultArticle = $mysqli->prepare($sSQL);
		$sParam = '%' . $sParam . '%';

		
		if($sType == "third"){
			$resultArticle->bind_param('ss', $sParam, $sParam);
		}else{
			
			$resultArticle->bind_param('s', $sParam);
			
		}
	
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

//
// Methode : Setzt den Namen für die Bilder fest
//
function __getPictureNameFormated($title){
	return strtolower(preg_replace("/\,/", ".", preg_replace("/[^a-zA-Z0-9\,]+/", "_", $title)));
}

//
// Methode : Setzt den Bild-Namen zusammen.
//
function __getImageInfo($praefix, $articleName, $postfix){
	return $praefix . __getPictureNameFormated($articleName);
}

?>