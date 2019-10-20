jQuery(document).ready(function($){
	//if you change this breakpoint in the style.css file (or _layout.scss if you use SASS), don't forget to update this value as well
	var $L = 1200,
		$menu_navigation = $('#main-nav'),
		$cart_trigger = $('#cd-cart-trigger'),
		$list_trigger = $('#cd-list-trigger'),
		$hamburger_icon = $('#cd-hamburger-menu'),
		$lateral_cart = $('#cd-cart'),
		$lateral_list = $('#cd-list'),
		$shadow_layer = $('#cd-shadow-layer');
	
	reload_list_and_cart();

	//open lateral menu on mobile
	/*$hamburger_icon.on('click', function(event){
		event.preventDefault();
		//close cart panel (if it's open)
		$lateral_cart.removeClass('speed-in');
		toggle_panel_visibility($menu_navigation, $shadow_layer, $('body'));
	});*/

	//open cart
	$cart_trigger.on('click', function(event){
		event.preventDefault();
		//close lateral menu (if it's open)
		$menu_navigation.removeClass('speed-in');
		toggle_panel_visibility($lateral_cart, $shadow_layer, $('body'));
	});
	
	//open list
	$list_trigger.on('click', function(event){
		event.preventDefault();
		//close lateral menu (if it's open)
		$menu_navigation.removeClass('speed-in');
		toggle_panel_visibility($lateral_list, $shadow_layer, $('body'));
	});

	//close lateral cart or lateral menu
	$shadow_layer.on('click', function(){
		$shadow_layer.removeClass('is-visible');
		// firefox transitions break when parent overflow is changed, so we need to wait for the end of the trasition to give the body an overflow hidden
		if( $lateral_cart.hasClass('speed-in') ) {
			$lateral_cart.removeClass('speed-in').on('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(){
				$('body').removeClass('overflow-hidden');
			});
			$menu_navigation.removeClass('speed-in');
		} else {
			$menu_navigation.removeClass('speed-in').on('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(){
				$('body').removeClass('overflow-hidden');
			});
			$lateral_cart.removeClass('speed-in');
		}
		//console.log("shadow-layer");
	});
	
	//close lateral list or lateral menu
	$shadow_layer.on('click', function(){
		$shadow_layer.removeClass('is-visible');
		// firefox transitions break when parent overflow is changed, so we need to wait for the end of the trasition to give the body an overflow hidden
		if( $lateral_list.hasClass('speed-in') ) {
			$lateral_list.removeClass('speed-in').on('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(){
				$('body').removeClass('overflow-hidden');
			});
			$menu_navigation.removeClass('speed-in');
		} else {
			$menu_navigation.removeClass('speed-in').on('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(){
				$('body').removeClass('overflow-hidden');
			});
			$lateral_list.removeClass('speed-in');
		}
		console.log("shadow-layer");
		//reload_website();
		reload_list_and_cart();
	});

	//move #main-navigation inside header on laptop
	//insert #main-navigation after header on mobile
	move_navigation( $menu_navigation, $L);
	$(window).on('resize', function(){
		move_navigation( $menu_navigation, $L);
		
		if( $(window).width() >= $L && $menu_navigation.hasClass('speed-in')) {
			$menu_navigation.removeClass('speed-in');
			$shadow_layer.removeClass('is-visible');
			$('body').removeClass('overflow-hidden');
		}

	});
});

function toggle_panel_visibility ($lateral_panel, $background_layer, $body) {
	if( $lateral_panel.hasClass('speed-in') ) {
		// firefox transitions break when parent overflow is changed, so we need to wait for the end of the trasition to give the body an overflow hidden
		$lateral_panel.removeClass('speed-in').one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(){
			$body.removeClass('overflow-hidden');
		});
		$background_layer.removeClass('is-visible');

	} else {
		$lateral_panel.addClass('speed-in').one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(){
			$body.addClass('overflow-hidden');
		});
		$background_layer.addClass('is-visible');
	}
}

function move_navigation( $navigation, $MQ) {
	if ( $(window).width() >= $MQ ) {
		$navigation.detach();
		$navigation.appendTo('header');
	} else {
		$navigation.detach();
		$navigation.insertAfter('header');
	}
}

function reload_list_and_cart(){
	
	var user_id = $("#global_user_id")[0];
	
	if(user_id != undefined){
		
		//var user_id = $("#user_id")[0].textContent;
		user_id = user_id.textContent;
		var lang = $("html").attr("lang");	

		var dataString = "user=" + user_id + "&lang=" + lang;

		$.ajax({
			type: "POST",
			url: "http://192.168.178.41/ajax/get-list.php",
			data: dataString,
			cache: false,
			async: false,
			success: function(html){
				//console.log(html);
				$("#cd-list")[0].innerHTML = html;
				setListListeners();
			}
		});
		
		dataString = "user=" + user_id + "&lang=" + lang + "&place=2";
		
		$.ajax({
			type: "POST",
			url: "http://192.168.178.41/ajax/get-list.php",
			data: dataString,
			cache: false,
			async: false,
			success: function(html){
				//console.log(html);
				$("#cd-cart")[0].innerHTML = html;
				setCartListeners();
			}
		});
	}

}

function setListListeners(){
	
	//var moveButtons = $(".list-move")
	
	//for(var i=0; i<=$(".list-move").length; i++){
		$(".list-move").on("click", function(e){
			console.log(".list-move");
			moveList(e)
		});
	
		$(".list-remove").on("click", function(e){
			console.log(".list-remove");
			removeList(e);
		});
	//}
	
}

function setCartListeners(){
	
	//var moveButtons = $(".list-move")
	
	//for(var i=0; i<=$(".list-move").length; i++){
		$(".cart-move").on("click", function(e){
			console.log(".cart-move");
			moveCart(e)
		});
	
		$(".cart-checkout").on("click", function(e){
			console.log("cart-checkout");
			checkoutCart(e);
		});
	
		$(".cart-total-checkout").on("click", function(e){
			console.log("cart-total-checkout");
			totalCheckoutCart(e);
		});
	
		$(".list-remove").on("click", function(e){
			console.log(".list-remove");
			removeList(e);
		});
	
	//}
	
}

function moveList(e){
	console.log("moveList");

	//console.log(e.target.parentElement.parentNode);

	var user_id = e.target.parentElement.parentNode.childNodes.item(1).innerText;
	var article_id = e.target.parentElement.parentNode.childNodes.item(2).innerText;
	var list = e.target.parentElement.parentNode.getAttribute("id");
	var listResult = e.target.parentElement.parentNode;
	var list_id = list.replace("result-", "");
	var response = "";

	/*console.log(user_id);
	console.log(article_id);
	console.log(listResult);
	console.log(list_id);*/

	if(user_id != null && article_id != null && list_id != null){

		var dataString = "user=" + user_id + "&article=" + article_id + "&list=" + list_id + "&move=1";

		$.ajax({
			type: "POST",
			url: "http://192.168.178.41/ajax/manage-cart.php",
			data: dataString,
			cache: false,
			async: false,
			success: function(html){
			//console.log(html);
			response = html;
			}
		});
	}

	if(response == "success"){
		//console.log(e.target.parentElement.parentNode.innerHTML);
		//$(".cd-cart-items")[0].innerHTML = e.target.parentElement.parentNode.innerHTML;
		$(e.target.parentElement.parentNode).animate({
				left: '400px', 
				opacity: '0.5'}, 200, "linear", function() {
			e.target.parentElement.parentNode.remove();
		});
	}

}

function removeList(e){
	console.log("removeList");

	var user_id = e.target.parentElement.parentNode.childNodes.item(1).innerText;
	var article_id = e.target.parentElement.parentNode.childNodes.item(2).innerText;
	var list = e.target.parentElement.parentNode.getAttribute("id");
	var listResult = e.target.parentElement.parentNode;
	var listResult2 = e.target.parentElement.parentNode.parentNode;
	var list_id = list.replace("result-", "");
	var response = "";

	console.log(user_id);
	console.log(article_id);
	console.log(list_id);

	if(user_id != null && article_id != null && list_id != null){

		var dataString = "user=" + user_id + "&article=" + article_id + "&list=" + list_id;

		$.ajax({
			type: "POST",
			url: "http://192.168.178.41/ajax/manage-cart.php",
			data: dataString,
			cache: false,
			async: false,
			success: function(html){
				//console.log(html);
				response = html;
			}
		});
	}

	if(response == "success"){

		$(e.target.parentElement.parentNode).animate({
				left: '400px', 
				opacity: '0.5'}, 200, "linear", function() {
			e.target.parentElement.parentNode.remove();
		});
	}

	return false;
}

function moveCart(e){
	console.log("moveCart");

	//console.log(e.target.parentElement.parentNode.childNodes);

	var user_id = e.target.parentElement.parentNode.childNodes.item(1).innerText;
	var article_id = e.target.parentElement.parentNode.childNodes.item(2).innerText;

	var list = e.target.parentElement.parentNode.getAttribute("id");
	var listResult = e.target.parentElement.parentNode;
	var list_id = list.replace("result-", "");
	var response = "";

	console.log(user_id);
	console.log(article_id);
	console.log(listResult);
	console.log(list_id);

	if(user_id != null && article_id != null && list_id != null){

		var dataString = "user=" + user_id + "&article=" + article_id + "&list=" + list_id + "&move=0";

		$.ajax({
			type: "POST",
			url: "http://192.168.178.41/ajax/manage-cart.php",
			data: dataString,
			cache: false,
			async: false,
			success: function(html){
				//console.log(html);
				response = html;
			}
		});
	}

	if(response == "success"){
		$(e.target.parentElement.parentNode).animate({
				left: '400px', 
				opacity: '0.5'}, 200, "linear", function() {
			e.target.parentElement.parentNode.remove();
		});
	}

}

function checkoutCart(e){
	console.log("checkoutCart");

	console.log(e.target.parentElement.parentNode.childNodes);


	var user_id = e.target.parentElement.parentNode.childNodes.item(1).innerText;
	var article_id = e.target.parentElement.parentNode.childNodes.item(2).innerText;

	var list = e.target.parentElement.parentNode.getAttribute("id");
	var list_id = list.replace("result-", "");
	var response = "";

	console.log(user_id);
	console.log(article_id);
	//console.log(listResult);
	console.log(list_id);

	if(user_id != null && article_id != null && list_id != null){

		var dataString = "user=" + user_id + "&article=" + article_id + "&list=" + list_id + "&bought=1";

		$.ajax({
			type: "POST",
			url: "http://192.168.178.41/ajax/manage-cart.php",
			data: dataString,
			cache: false,
			async: false,
			success: function(html){
				//console.log(html);
				response = html;
			}
		});
	}

	if(response == "success"){
		$(e.target.parentElement.parentNode).animate({
				left: '400px', 
				opacity: '0.5'}, 200, "linear", function() {
			e.target.parentElement.parentNode.remove();
		});
	}

}

function totalCheckoutCart(e){
	console.log("totalCheckoutCart");
	
	var articles = e.target.parentNode.children.item(1).children;
	//console.log(articles);
	var response = "";

	var i = 0;

	//console.log("Lenght" + articles.length);

	var checkout_articles = [];

	while(i < articles.length){

		//console.log(articles.item(i));

		var user_id = articles.item(i).children.item(0).innerText;
		//console.log(user_id);

		var article_id = articles.item(i).children.item(1).innerText;
		//console.log(article_id);

		var list = articles.item(i).getAttribute("id");
		var list_id = list.replace("result-", "");
		//console.log(list_id);

		checkout_articles[i] = {article_id: article_id, list: list_id, user_id: user_id};
		//console.log(article_id + ":" + list_id + ":" + user_id);

		i++;
	}
	//console.log(checkout_articles);


	if(checkout_articles.length > 0){
		var articles  = JSON.stringify(checkout_articles);
		var dataString = "checkout=" + articles + "&bought=1";


		$.ajax({
			type: "POST",
			url: "http://192.168.178.41/ajax/manage-cart.php",
			data: dataString,
			cache: false,
			async: false,
			success: function(html){
				//console.log(html);
				response = html;
			}
		});
	}

	if(response == "success"){
		//document.getElementsByClassName("cd-cart-items")[0].innerHTML = "";
		$($(".cd-cart-items")[0]).animate({
				left: '400px', 
				opacity: '0.5'}, 200, "linear", function() {
			$(".cd-cart-items")[0].innerHTML = "";;
		});
	}
}

function removeCart(e){
	
}

/*function reload_website(){
		if(window.location.href == "http://192.168.178.41/site/login"){
			window.location.href = "http://192.168.178.41/";
		}else{
			location.reload();
		}
}*/