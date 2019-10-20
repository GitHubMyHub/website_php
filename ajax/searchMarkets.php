<?php //echo 'This comes from php file'; die(); ?>
<?php

//include("config.php");

	switch(intval($_GET["distance"])){
		
		case 10:
			$factor = 0.0089932 * 10;
			//echo $factor;
			break;
		case 20:
			$factor = 0.0089932 * 20;
			break;
		case 30:
			$factor = 0.0089932 * 30;
			break;
		case 40:
			$factor = 0.0089932 * 40;
			break;
		default:
			$factor = 0.0089932 * 40;
			break;
	}
		   
	$distance = $factor;

	$maxLat = floatval($_GET["latitude"]) + $distance;
	$minLat = floatval($_GET["latitude"]) - $distance;

	$maxLong = floatval($_GET["longitude"]) + $distance;
	$minLong = floatval($_GET["longitude"]) - $distance;
	
	$sSQL = "SELECT * FROM market_clean "
		  . "WHERE (market_latitude<='" . $maxLat . "' AND market_latitude>='" . $minLat . "') "
		  . "AND (market_longitude<='" . $maxLong . "' AND market_longitude>='" . $minLong . "') ";
	
//SELECT * FROM market_clean WHERE market_latitude BETWEEN '51.8324125' AND '8.439784'

	$oArray = array();
	$int = 0;

	$mysqli2 = new mysqli("localhost", "root", "root", "aldi");
	
	// Prüft ob das Charset UTF-8 ist
	if(!$mysqli2->set_charset("utf8")) {
		echo "Fehler beim laden von UTF-8 " . $mysqli->error;
	}

	//echo $sSQL;
	
	$result = $mysqli2->query($sSQL) or die(mysql_error());

	while($zeile = $result->fetch_assoc()){
		//echo htmlentities($zeile["market_name"], ENT_QUOTES, "UTF-8");
		$zeile = array($zeile["market_id"], htmlentities($zeile["market_name"],  ENT_COMPAT | ENT_XHTML, 'UTF-8'), htmlentities($zeile["market_street"]), htmlentities($zeile["market_plz"]), htmlentities($zeile["market_city"]), htmlentities($zeile["market_company"]), htmlentities($zeile["market_latitude"]), htmlentities($zeile["market_longitude"]), htmlentities($zeile["place_id"]));
		//print_r($zeile);
		array_push($oArray, $zeile);
		$int++;
	}

	//print_r($oArray);
	echo json_encode($oArray);
?>