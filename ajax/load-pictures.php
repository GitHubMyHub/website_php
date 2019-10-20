<?php
	//header('Content-Type: application/json');
	include("../include/functions.php");

	if(isset($_POST["user_id"])){
		
		$user_id = strval($_POST["user_id"]);
		//$user_id = 1;

		$path = "../uploads/article_upload/user_id_" . $user_id . "/";
		$files = getUploadPictures($path);

		//print_r($files);

		$stdClass = new stdClass();

		$stdClass->results = (object) $files;

		echo json_encode($stdClass, JSON_UNESCAPED_SLASHES);
		
	} 

?>