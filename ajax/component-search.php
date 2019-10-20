<?php

include("../include/config.php");
//include("include/picture-class.php");
$mysqli = new db();

if($_POST){

	$q = preg_replace("/-/", "", strtolower($mysqli->real_escape_string($_POST['search'])));
		
	$sSQL = "SELECT * FROM bc_components "
		  . "WHERE LOWER(components_name) LIKE ? "
		  . " ORDER BY components_id ASC LIMIT 5;";

	//echo $sSQL;
	$articleResult = __prepareStmtArticle($mysqli, $sSQL, $q);
	

	
	//$articleResult = prepareStmtArticle($mysqli, $sSQL, $q) or die(mysql_error());
	
	//$articleResult = $mysqli->query($sSQL) or die(mysql_error());
	
	while($zeile = $articleResult->fetch_assoc()){
		$components_id		= $zeile["components_id"];
		$componentsname		= $zeile["components_name"];
		//$articledesc		= strip_tags($zeile["article_description"]);
        $b_componentsname 		= '<strong>'.$q.'</strong>';
        $final_componentsname 	= str_ireplace($q, $b_componentsname, $componentsname);
		
		
		
		?>
           
            <a id="dropdown" href="<?php echo $components_id; ?>"><div class="show" align="left">
                <span class="id"><?php echo $components_id; ?></span><span class="name"><?php echo $final_componentsname; ?></span><br/></span><br/>
            </div></a>
		
		<?php
	}
}

//
// Methode : Funktion für die Erstellung eines prepared Statements
//         : an die Zutaten-Datenbank.
function __prepareStmtArticle($mysqli, $sSQL, $sParam) {
		$resultArticle = $mysqli->prepare($sSQL);
		$sParam = '%' . $sParam . '%';
		//echo $sSQL;
		//echo $sParam;
	

		$resultArticle->bind_param('s', $sParam);
	
		try {
			$resultArticle->execute();
		} catch (Exception $e){
			echo "Fehler in der SQL Anweisung.";
		}
	
		$result = $resultArticle->get_result();

		return $result;
}
?>