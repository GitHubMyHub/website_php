<?php
	include("include/config.php");
	include("include/functions.php");

	// Settings
	$sSQL = "SELECT set_bezeichnung, set_wert "
		  . "FROM bc_settings WHERE set_kategory='bc-settings'";
	$mysqli = new db();
	$resultSettings = $mysqli->query($sSQL);
	$GLOBALS['settings'] = sql2Array($resultSettings);

	include("include/lang.inc.php");
	include("include/field-class.php");
	include("firebase_token/index.php");
	include("include/login.inc.php");
	include("include/picture-class.php");
	include("include/article-class.php");
	//include("search.php");

	  // Session
	  session_start();

	  $oAuth = new Login($mysqli);
	  $oAuth->setAuth();

	include("include/view_handling.inc.php");
	include("include/dynamicinline.php");
	
	include("include/hitcounter-class.php");
	include("include/communication-class.php");

	
	if(isset($_SESSION["photoURL"])){
		//echo $_SESSION["photoURL"];
	}


	

	if(!empty($_GET["language"]) and $_SESSION['speak'] != ""){
		//echo $_GET["language"];
		switch($_GET["language"]){
			case "en":
				$_SESSION['speak'] = "en";
				break;
			case "de":
				$_SESSION['speak'] = "de";
				break;
			default:
				$_SESSION['speak'] = "de";
				break;
		}
	}else if(empty($_SESSION['speak'])){
		$_SESSION['speak'] = "de";
	}
		
	
?>
<!doctype html>
<html class="no-js" lang="<?php echo $_SESSION['speak']; ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo $settings["bc-titel"]; ?></title>
        <meta name="description" content="<?php echo $settings["bc-description"]; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<meta property="og:url"           content="http://localhost/bootstrap/site/list_articles/article/1" />
		<meta property="og:type"          content="website" />
		<meta property="og:title"         content="Your Website Title" />
		<meta property="og:description"   content="Your description" />
		<meta property="og:image"         content="http://localhost/bootstrap/uploads/article/1/no_name_thumbnail.png" />
        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        
        <?php
			//echo $settings["bc-website-url"];
			echo getInlineFont($settings["bc-website-url"]);
		?>

        <link rel="stylesheet" href="<?php echo $settings["bc-website-url"]; ?>css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo $settings["bc-website-url"]; ?>css/main.css">
        <link rel="stylesheet" href="<?php echo $settings["bc-website-url"]; ?>css/style2.css">
        <link rel="stylesheet" href="<?php echo $settings["bc-website-url"]; ?>css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo $settings["bc-website-url"]; ?>css/bootstrap-datetimepicker.css">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css">

		<!--<link rel="stylesheet" href="<?php echo $settings["bc-website-url"]; ?>css/root.css">-->
		
		<?php echo "<script type='text/javascript' src='" . $settings['bc-website-url'] . "js/vendor/jquery-3.1.1.min.js'></script>"; ?>
		
		<?php echo "<script type='text/javascript' src='" . $settings['bc-website-url'] . "js/vendor/bootstrap.min.js'></script>"; ?>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<?php //echo "<script type='text/javascript' src='" . $settings['bc-website-url'] . "js/vendor/jquery-ui.min.js'></script>"; ?>
		
		<!--<?php echo "<script type='text/javascript' src='" . $settings['bc-website-url'] . "js/scriptwj.js'></script>"; ?>-->
		
		<script src="<?php echo $settings["bc-website-url"]; ?>js/jquery.maskedinput.min.js" type="text/javascript"></script>
		
		<?php echo "<script type='text/javascript' src='" . $settings['bc-website-url'] . "js/main.js'></script>"; ?>
		<?php echo "<script type='text/javascript' src='" . $settings['bc-website-url'] . "js/main2.js'></script>"; ?>
		<?php echo "<script type='text/javascript' src='" . $settings['bc-website-url'] . "js/upload.js'></script>"; ?>
		
		<?php echo "<script type='text/javascript' src='" . $settings['bc-website-url'] . "js/bootstrap-datetimepicker.js' charset='UTF-8'></script>"; ?>
		<?php echo "<script type='text/javascript' src='" . $settings['bc-website-url'] . "js/bootstrap-datetimepicker.de.js' charset='UTF-8'></script>"; ?>

		<!-- FIREBASE -->
		<script src="https://www.gstatic.com/firebasejs/4.8.1/firebase.js"></script>
		<script src="https://cdn.firebase.com/libs/firebaseui/1.0.0/firebaseui.js"></script>
		<link type="text/css" rel="stylesheet" href="https://cdn.firebase.com/libs/firebaseui/1.0.0/firebaseui.css" />
  
		<!-- GOOGLE Plus -->
		<!--<script src="https://apis.google.com/js/platform.js" async defer>
		  {lang: 'de'}
		</script>-->

<?php
		//echo getInlineJquery($settings['bc-website-url']);

		//if(!isset($_COOKIE["barcodede"])) {
?>
	<!--<script type="text/javascript">
	$(document).ready(function(){
		$.ajax({
			url: 'https://fonts.googleapis.com/css?family=Droid+Sans',
			dataType: "css"
		});
		$.ajax({
			url: 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js',
			dataType: "script"
		});
	});
	</script>-->
	
<?php
		//}
?>
  
<?php
	//$cookie_name = "barcodede";

	/*if(!isset($_COOKIE["barcodede"])) {
		//echo "Cookie named '" . $cookie_name . "' is not set!";

		$cookie_value = 1;
		setcookie($cookie_name, $cookie_value);
	} else {
		//echo "Cookie '" . $cookie_name . "' is set!<br>";
		//echo "Value is: " . $_COOKIE[$cookie_name];
	}*/

?>  
    </head>
    <body data-spy="scroll" data-target="#myScrollspy" data-offset="230">
    
	<!--<div id="fb-root"></div>
	<script>(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = 'https://connect.facebook.net/de_DE/sdk.js#xfbml=1&version=v2.11';
		fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
	</script>-->

<!-- NAVIGATION -->
<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo $settings["bc-website-url"]; ?>"><img alt="Logo" src="<?php echo $settings["bc-website-url"]; ?>img/logo.png"></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
      	<li><a href="<?php echo $settings["bc-website-url"]; ?>site/list_news"><?php echo $lang[$_SESSION['speak']]['News-Title']; ?></a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $lang[$_SESSION['speak']]['Article-Title']; ?> <span class="caret"></span></a>
	            <ul class="dropdown-menu multi-column columns-2">
		            <div class="row">
			            <div class="col-sm-6">
				            <ul class="multi-column-dropdown">
					            <li role="presentation" class="dropdown-header"><?php echo $lang[$_SESSION['speak']]['Alphabetic']; ?></li>
            <li><a href="<?php echo $settings["bc-website-url"]; ?>site/list_article/filter/a-b">A - B</a></li>
            <li><a href="<?php echo $settings["bc-website-url"]; ?>site/list_article/filter/c-d">C - D</a></li>
            <li><a href="<?php echo $settings["bc-website-url"]; ?>site/list_article/filter/e-f">E - F</a></li>
            <li><a href="<?php echo $settings["bc-website-url"]; ?>site/list_article/filter/g-h">G - H</a></li>
            <li><a href="<?php echo $settings["bc-website-url"]; ?>site/list_article/filter/i-j">I - J</a></li>
            <li><a href="<?php echo $settings["bc-website-url"]; ?>site/list_article/filter/k-l">K - L</a></li>
			            <li class="divider"></li>
				            </ul>
			            </div>
			            <div class="col-sm-6">
				            <ul class="multi-column-dropdown">
				            <li>&nbsp;</li>
				            <li><a href="<?php echo $settings["bc-website-url"]; ?>site/list_article/filter/m-n">M - N</a></li>
            <li><a href="<?php echo $settings["bc-website-url"]; ?>site/list_article/filter/o-p">O - P</a></li>
            <li><a href="<?php echo $settings["bc-website-url"]; ?>site/list_article/filter/q-r">Q - R</a></li>
            <li><a href="<?php echo $settings["bc-website-url"]; ?>site/list_article/filter/s-t">S - T</a></li>
            <li><a href="<?php echo $settings["bc-website-url"]; ?>site/list_article/filter/u-v">U - V</a></li>
            <li><a href="<?php echo $settings["bc-website-url"]; ?>site/list_article/filter/wxyz">WXYZ</a></li>
				            <li class="divider"></li>
					            <li><a href="<?php echo $settings["bc-website-url"]; ?>site/list_market"><?php echo $lang[$_SESSION['speak']]['Articles in Market']; ?></a></li>
					            <li><a href="<?php echo $settings["bc-website-url"]; ?>site/list_market_radar"><?php echo $lang[$_SESSION['speak']]['Market radar']; ?></a></li>
				            </ul>
			            </div>
		            </div>
	            </ul>  
        </li>
      </ul>
      <ul class="nav navbar-nav pull-right">
<?php
	  
	if($oAuth->getLoginOk())
	{		
?>
		<span id="global_user_id" class="hiding"><?php echo $oAuth->getSessionToId(); ?></span>
		<li class="dropdown">
			<!--<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user"></span> <?php echo $lang[$_SESSION['speak']]['User']; ?> <span class="caret"></span></a>-->
			
			<?php
	 			if($_SESSION["photoURL"] == ""){
	 		?>
			
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class='glyphicon glyphicon-user'></span> <?php echo $lang[$_SESSION['speak']]['User']; ?> <span class="caret"></span></a>
			
			<?php
				}else{
			?>
			
			<a href="#" class="dropdown-toggle user-menu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img style='width: 30px; height: 30px' class='img-circle' src="<?php echo $_SESSION["photoURL"]; ?>" />  <span class="caret"></span></a>
			
			<?php
				}
			?>
			
			<ul class="dropdown-menu">
				<li><a type="submit" href="<?php echo $settings["bc-website-url"]; ?>site/login/list_profile"><span class="glyphicon glyphicon-user"></span> <?php echo $lang[$_SESSION['speak']]['Profile']; ?></a></li>
				
				<li><a type="submit" href="<?php echo $settings["bc-website-url"]; ?>site/login/list_favorites"><span class="glyphicon glyphicon-bookmark"></span> <?php echo $lang[$_SESSION['speak']]['Favorites']; ?></a></li>
				
				<li><a type="submit" href="<?php echo $settings["bc-website-url"]; ?>site/login/list_shopping_lists"><span class="glyphicon glyphicon-shopping-cart"></span> <?php echo $lang[$_SESSION['speak']]['Shopping List']; ?></a></li>
				
				<li><a type="submit" href="<?php echo $settings["bc-website-url"]; ?>site/login/list_ingredient_filters"><span class="glyphicon glyphicon-filter"></span> <?php echo $lang[$_SESSION['speak']]['Ingredient-Filters']; ?></a></li>
				
				<li><a type="submit" href="<?php echo $settings["bc-website-url"]; ?>site/login/list_filters"><span class="glyphicon glyphicon-filter"></span> <?php echo $lang[$_SESSION['speak']]['Filters']; ?></a></li>

				<li><a type="button" id="btnSessionLogout" href="<?php echo $settings["bc-website-url"]; ?>?logout=1"><span class="glyphicon glyphicon-off"></span> <?php echo $lang[$_SESSION['speak']]['Logout']; ?></a></li>
			</ul>
		</li>
<?php
	}else{
?>
		<li>
			<a href="<?php echo $settings["bc-website-url"]; ?>site/login" ><span class="glyphicon glyphicon-user"></span> <?php echo $lang[$_SESSION['speak']]['Login']; ?></a>
		</li>
<?php
		 }
?>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $lang[$_SESSION['speak']]['Language']; ?> <span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li><a type="submit" href="<?php echo $settings["bc-website-url"]; ?>?language=en"><?php echo $lang[$_SESSION['speak']]['English']; ?></a></li>
				<li><a type="submit" href="<?php echo $settings["bc-website-url"]; ?>?language=de"><?php echo $lang[$_SESSION['speak']]['German']; ?></a></li>
			</ul>
		</li>
	<?php	
		if($oAuth->getLoginOk()){
	?>
		<li><a href="<?php echo $settings["bc-website-url"]; ?>site/login/list_stock"><span class="glyphicon glyphicon-home"></span> <?php echo $lang[$_SESSION['speak']]['Stock']; ?></a></li>
		<li id="cd-list-trigger">
			<a href="#0" ><span>&nbsp;</span></a>
		</li>
		<li id="cd-cart-trigger">
			<a href="#0" ><span>&nbsp;</span></a>
		</li>


		<div id="cd-shadow-layer"></div>

		<div id="cd-list"></div>
				
	<?php
	} /* LOGIN ENDE */
	?>

	<div id="cd-cart"></div>
		
		
    </ul><!-- /.cd-list-items -->
        
        
	  </ul>
   </div><!-- /.collapse -->
  </div><!-- /.container-fluid -->
</nav>

<!-- NAVIGATION --> 
<div class="container">
<?php //echo $incFile; ?>
<?php include($incFile); ?>
</div>

<!-- PAGE NAV -->

<div class="container">

</div>

<!-- PAGE NAV -->

<!-- FOOTER -->

<div class="container text-center">
    <hr />
  <div class="row">
    <div class="col-lg-12">
      <div class="col-lg-3 col-md-6 col-xs-12">
      <h4>Website</h4>
        <ul class="fs-footer-website">
            	<li><a href="<?php echo $settings["bc-website-url"]; ?>"><?php echo $lang[$_SESSION['speak']]['Home']; ?></a>
                <li><a href="<?php echo $settings["bc-website-url"]; ?>site/contact"><?php echo $lang[$_SESSION['speak']]['Contact']; ?></a>
                <li><a href="<?php echo $settings["bc-website-url"]; ?>site/sitemap"><?php echo $lang[$_SESSION['speak']]['Sitemap']; ?></a>
        </ul>
      </div>
      <div class="col-lg-3 col-md-6 col-xs-12">
        <!--<h4>Beispiel</h4>
        <ul class="fs-footer-artist">
            	<li><a href="#">Beispiel</a>
        </ul>-->
      </div>
      <div class="col-lg-3 col-md-6 col-xs-12">
      <!--<h4>Beispiel</h4>
        <ul class="fs-footer-new">
            	<li><a href="#">Beispiel</a>        
        </ul>-->
      </div>
      <div class="col-lg-3 col-md-6 col-xs-12">
      <h4><?php echo $lang[$_SESSION['speak']]['Be Social!']; ?></h4>
        <ul class="fs-footer-info">
            	<li><a href="<?php echo $settings["bc-facebook-url"]; ?>"><img title="Facebook logo" alt="Facebook logo" src="<?php echo $settings["bc-website-url"]; ?>img/icon-facebook.png"></a>
                <li><a href="<?php echo $settings["bc-twitter-url"]; ?>"><img  title="Twitter logo" alt="Twitter logo" src="<?php echo $settings["bc-website-url"]; ?>img/icon-twitter.png"></a>
                <li><a href="<?php echo $settings["bc-googleplus-url"]; ?>"><img  title="Google+ logo" alt="Google+ logo" src="<?php echo $settings["bc-website-url"]; ?>img/icon-google-plus.png"></a>
        </ul>
      </div>  
    </div>
  </div>
  
  <nav>
  <hr>
    <div class="row">
        <div class="col-lg-12">
            <ul class="nav-justified">
                <li><a href="#"><?php echo $settings["bc-footer-text"]; ?></a></li>
                <li><a href="<?php echo $settings["bc-website-url"]; ?>site/service"><?php echo $lang[$_SESSION['speak']]['Terms of Service']; ?></a></li>
                <li><a href="<?php echo $settings["bc-website-url"]; ?>site/privacy"><?php echo $lang[$_SESSION['speak']]['Privacy']; ?></a></li>
            </ul>
        </div>
    </div>
  </nav>
</div>
<?php echo "<script type='text/javascript' src='" . $settings['bc-website-url'] . "/js/firebase-ui.js'></script>"; ?>

<!-- FOOTER -->

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <?php //echo $settings["bc-google-analytics"]; ?>
    </body>
</html>
