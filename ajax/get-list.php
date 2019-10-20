<?php
	include("../include/config.php");
	include("../include/functions.php");
	include("../include/lang.inc.php");

	// Settings
	$sSQL = "SELECT set_bezeichnung, set_wert "
		  . "FROM bc_settings WHERE set_kategory='bc-settings'";
	$mysqli = new db();
	$resultSettings = $mysqli->query($sSQL);
	$GLOBALS['settings'] = sql2Array($resultSettings);


	if(isset($_POST["user"]) && !empty($_POST["user"]) && isset($_POST["lang"]) && !empty($_POST["lang"]) && !isset($_POST["place"])){
		$user_id = strval($_POST["user"]);
		$speak = strval($_POST["lang"]);
		
		$sSQL = "SELECT * FROM bc_shopping_lists "
			  . "WHERE user_id_fk=?"
			  . " AND shopping_lists_bought=2";
		
		//echo $sSQL;
				  
		//$result = $this->mysqli->query($sSQL);
		$stmt = $mysqli->prepare($sSQL);
		$stmt->bind_param("s", $user_id);
		$stmt->execute();
		
		$result = $stmt->get_result();
	?>
		<h2><?php echo $lang[$speak]['Shopping List']; ?></h2>
		<ul class="cd-list-items">

	<?php
		
		while($shopping_lists = $result->fetch_assoc()){
	?>
				
			<li><?php echo $shopping_lists["shopping_lists_name"] ?><a><button class="btn pull-right" data-toggle="collapse" data-target="#demo-<?php echo $shopping_lists["shopping_lists_id"] ?>"><span class="glyphicon glyphicon-chevron-down"></span></button></a></li>
				
				
			<div id="demo-<?php echo $shopping_lists["shopping_lists_id"] ?>" class="collapse in">
				<ul>

				<?php

				$result2 = getCartContent($mysqli, 1, $user_id);
				while($shopping_list = $result2->fetch_assoc()){

					if($shopping_lists["shopping_lists_id"] == $shopping_list["shopping_lists_id"]){


						// Image-name
						$imageInfo = getImageInfo($shopping_list["picture_praefix"], 
									 $shopping_list["article_name"], "");

				?>
					<li id="result-<?php echo $shopping_lists["shopping_lists_id"]; ?>">
				<?php
					if($shopping_list["article_user_id_fk"] != 0 && $shopping_list["shopping_list_picture_id"] == 0){
						$src = $GLOBALS['settings']['bc-website-url'] . $GLOBALS['settings']['bc_article'] . $shopping_list["picture_id"] . "/" . $imageInfo . "_thumbnail.png";

					}else if($shopping_list["article_user_id_fk"] == 0 && $shopping_list["shopping_list_picture_id"] != 0){

						$path = "../uploads/article_upload/user_id_" . $user_id . "/shopping_list_id_" . $shopping_list["shopping_list_picture_id"] . "/";
						$pathAbsolute = "/uploads/article_upload/user_id_" . $user_id . "/shopping_list_id_" . $shopping_list["shopping_list_picture_id"] . "/";
						
						$file_name = getUploadPicture($path);
						$src = $GLOBALS['settings']['bc-website-url'] . $pathAbsolute . $file_name;
					}else{
						$src = $GLOBALS['settings']['bc-website-url'] . "/img/info_picture.png";
					}
				?>
					
						<span class="hiding" id="user_id"><?php echo $shopping_list["user_id_fk"] ?></span><span class="hiding" id="article_id"><?php echo $shopping_list["shopping_list_id"] ?></span><img style="width: 50px; height: 50px;" src='<?php echo $src; ?>'>
						<span class="cd-qty"><?php echo $shopping_list["shopping_list_quantity"] ?>x</span> <?php echo $shopping_list["shopping_list_name"] ?>
						<!--<div class="cd-price">$9.99</div></li>-->

						<a class="cd-item-remove cd-img-replace list-move" style="right: 5em;"><button title="<?php echo $lang[$speak]['List Move']; ?>" class="btn btn-xs btn-success pull-right"><span class="glyphicon glyphicon-arrow-right"></span></button></a>

						<a href="<?php echo $settings["bc-website-url"]; ?>site/login/edit_shopping_list/sublist/<?php echo $shopping_lists["shopping_lists_id"] ?>/article/<?php echo $shopping_list["shopping_list_id"] ?>" class="cd-item-remove cd-img-replace" style="right: 3em;"><button title="<?php echo $lang[$speak]['Cart Edit']; ?>" class="btn btn-xs btn-primary pull-right"><span class="glyphicon glyphicon-pencil"></span></button></a>

						<a class="cd-item-remove cd-img-replace list-remove" style="right: 1em;"><button title="<?php echo $lang[$speak]['Cart Delete']; ?>" class="btn btn-xs btn-danger pull-right"><span class="glyphicon glyphicon-remove"></span></button></a>
					</li>
					<?php
						} /* ENDE IF STMT */
					} /* ENDE WHILE STMT */
					?>
				</ul> <!-- /.cd-list-items -->
			</div>	
				
		<?php
		} /* ENDE WHILE STMT */
		?>
		</ul>
<?php
	}else if(isset($_POST["user"]) && !empty($_POST["user"]) && isset($_POST["lang"]) && !empty($_POST["lang"]) && isset($_POST["place"])){
		$user_id = strval($_POST["user"]);
		$speak = strval($_POST["lang"]);
?>
	<h2><?php echo $lang[$speak]['Cart']; ?></h2>
		<ul class="cd-cart-items">
			<?php

				$result = getCartContent($mysqli, 2, $user_id);
				while($shopping_list = $result->fetch_assoc()){
					
					// Image-name
					$imageInfo = getImageInfo($shopping_list["picture_praefix"], 
										 $shopping_list["article_name"], "");
							
			?>
		
		
			<li id="result-<?php echo $shopping_list["shopping_lists_id"]; ?>">
			
				<?php
					if($shopping_list["article_user_id_fk"] != 0 && $shopping_list["shopping_list_picture_id"] == 0){
						$src = $GLOBALS['settings']['bc-website-url'] . $GLOBALS['settings']['bc_article'] . $shopping_list["picture_id"] . "/" . $imageInfo . "_thumbnail.png";
					}else if($shopping_list["article_user_id_fk"] == 0 && $shopping_list["shopping_list_picture_id"] != 0){

						$path = "../uploads/article_upload/user_id_" . $user_id . "/shopping_list_id_" . $shopping_list["shopping_list_picture_id"] . "/";
						$pathAbsolute = "/uploads/article_upload/user_id_" . $user_id . "/shopping_list_id_" . $shopping_list["shopping_list_picture_id"] . "/";
						$file_name = getUploadPicture($path);
						$src = $GLOBALS['settings']['bc-website-url'] . $pathAbsolute . $file_name;
					}else{
						$src = $GLOBALS['settings']['bc-website-url'] . "/img/info_picture.png";
					}
				?>
			
				<span class="hiding" id="user_id"><?php echo $shopping_list["user_id_fk"] ?></span><span class="hiding" id="article_id"><?php echo $shopping_list["shopping_list_id"] ?></span><img style="width: 50px; height: 50px;" src='<?php echo $src; ?>'>
				<span class="cd-qty"><?php echo $shopping_list["shopping_list_quantity"] ?>x</span> <?php echo $shopping_list["shopping_list_name"] ?>
				<!--<div class="cd-price">$9.99</div>-->
				
				<a class="cd-item-remove cd-img-replace cart-checkout" style="right: 7em;"><button title="<?php echo $lang[$speak]['Cart Success']; ?>" class="btn btn-xs btn-success pull-right"><span class="glyphicon glyphicon-ok"></span></button></a>
				
				<a class="cd-item-remove cd-img-replace cart-move" style="right: 5em;"><button title="<?php echo $lang[$speak]['Cart List']; ?>" class="btn btn-xs btn-primary pull-right"><span class="glyphicon glyphicon-arrow-left"></span></button></a>
				
				<a href="<?php echo $settings["bc-website-url"]; ?>site/login/edit_shopping_list/sublist/<?php echo $shopping_list["shopping_lists_id"] ?>/article/<?php echo $shopping_list["shopping_list_id"] ?>" class="cd-item-remove cd-img-replace" style="right: 3em;"><button title="<?php echo $lang[$speak]['Cart Edit']; ?>" class="btn btn-xs btn-primary pull-right"><span class="glyphicon glyphicon-pencil"></span></button></a>
				
				<a class="cd-item-remove cd-img-replace list-remove" style="right: 1em;"><button title="<?php echo $lang[$speak]['Cart Delete']; ?>" class="btn btn-xs btn-danger pull-right"><span class="glyphicon glyphicon-remove"></span></button></a>
			</li>
			<?php
				}
			?>
			
		</ul><!-- /.cd-cart-items -->

		<!--<div class="cd-cart-total">
			<p>Total <span>$39.96</span></p>
		</div>-->

		<a class="checkout-btn cart-total-checkout"><?php echo $lang[$speak]['Checkout']; ?></a>

		<p class="cd-go-to-cart"><a href="#0">&nbsp;</a></p>
<?php			
	}

	function getCartContent($mysqli, $place_id, $user_id){
		$user_id = strval($user_id);
		$place_id = intval($place_id);
		
		
		$sSQL = "SELECT shopping_list_id, bc_shopping_lists.shopping_lists_id, "
			  . "bc_shopping_lists.shopping_lists_name, "
			  . "bc_shopping_lists.user_id_fk, article_user_id_fk, shopping_list_picture_id, article_search, "
			  . "shopping_list_name, shopping_list_quantity, bc_unit.unit_name, "
			  . "shopping_list_durability, shopping_list_bought, article_name, "
			  . "picture_id, picture_source, picture_praefix, picture_postfix "
			  . "FROM bc_shopping_list "
			  . "INNER JOIN bc_shopping_lists "
			  . "ON bc_shopping_list.shopping_lists_id_fk = bc_shopping_lists.shopping_lists_id "
			  . "INNER JOIN bc_unit "
			  . "ON bc_shopping_list.unit_id_fk=bc_unit.unit_id "
			  . "LEFT JOIN bc_article "
			  . "ON article_user_id_fk=bc_article.article_id "
			  . "LEFT JOIN bc_picture "
			  . "ON bc_article.picture_id_fk=bc_picture.picture_id "
			  . "WHERE shopping_list_bought=2"
			  . " AND place_id_fk=?"
			  . " AND bc_shopping_list.user_id_fk=?";
		
		$stmt = $mysqli->prepare($sSQL);
		$stmt->bind_param("is", $place_id, $user_id);
		$stmt->execute();
		
		$result = $stmt->get_result();
		return $result;
		
	}

?>