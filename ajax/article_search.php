<?php
include("../include/config.php");
$mysqli = new db();


if(!empty($_POST["article_id"] and intval($_POST["article_id"]))){
	
	$q = intval($_POST['article_id']);
	
	$sSQL = "SELECT article_id, article_name, "
			  . "article_description, picture_id, "
			  . "picture_source, picture_praefix, picture_postfix "
			  . "FROM bc_article "
			  . "INNER JOIN bc_picture "
			  . "ON bc_article.picture_id_fk=bc_picture.picture_id "
              . "INNER JOIN bc_barcode "
			  . "ON bc_article.article_id = bc_barcode.article_id_fk "
			  . "AND picture_source='article' "
			  . "WHERE article_id=?";
	
	//echo $sSQL;
	
	$articleResult = __prepareStmtArticle($mysqli, $sSQL, $q);
	$article_info = array();
	
	while($zeile = $articleResult->fetch_assoc()){
	
		
		// Image-name
		$imageInfo = __getImageInfo($zeile["picture_praefix"], 
						$zeile["article_name"], 
						$zeile["picture_postfix"]);
		$article_info["article_image"] = $imageInfo;
		$article_info["article_name"] = $zeile["article_name"];
		$article_info["article_description"] = $zeile["article_description"];
		
		
		//print_r($article_info);
		echo json_encode($article_info);
	}
	
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