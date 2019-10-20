jQuery(function($){
	
	//$("#dialog").dialog({ autoOpen: false });
	
	
	$("#dialog").dialog({
		width: 500,
		autoOpen: false,
		show: {
		effect: "fade",
		duration: 200
		},
		hide: {
		effect: "fade",
		duration: 200
		}
	});
	
	$("#third").attr("checked","checked");
	
	$("#searchid").mask("***************************************",{placeholder:""});
    
	
	$.fn.first = function(){ 
		$("#searchid").mask("9999-9999",{placeholder:" "});
    }
	
	$.fn.second = function(){ 
		$("#searchid").mask("999-99999-999-9",{placeholder:" "});
    }
	
	$.fn.third = function(){ 
		$("#searchid").mask("***************************************",{placeholder:""});
    }

    $("#first").click(function(){
		console.log("#first");
        $.fn.first();
    });
	
    $("#second").click(function(){
		console.log("#second");
        $.fn.second();
    });
	
    $("#third").click(function(){
		console.log("#third");
        $.fn.third();
    });
	
	//$("#dialog").hide();
	
	//$("#dialog div a")
	
	
	$(".own-picture-search").on("click", function(e){
		//$("#dialog").dialog();

		//var pictureList = e.target;
		//var user_id = document.getElementsByName("user_id_fk")[0].value;
		var user_id = $("[name=user_id_fk]").val();
		
		//console.log(pictureList);

		console.log(user_id);
		var response;
		
		if(user_id != null){

			var dataString = "user_id=" + user_id;
			
			$.ajax({
				type: "POST",
				url: "http://192.168.178.41/ajax/load-pictures.php",
				data: dataString,
				cache: false,
				async: false,
				success: function(html){
					response = JSON.parse(html);
					
				}
			});
			
			var data = response.results;
			var result = "";
			for (var key in data) {
				if (data.hasOwnProperty(key)) {
					//console.log(data[key]);
					result += "<a class='select-own-image' href='javascript:undefined'><img src='http://192.168.178.41/" + data[key].Pathname.replace("../", "") + "' name='" + data[key].ID + "'></a>";
				}
			}
			
			
			//document.getElementsByClassName("own-pictures")[0].innerHTML = result;
			//pictures = document.getElementById("own-pictures");
			var pictures = $("#own-pictures");
			//pictures.insertAdjacentHTML("beforeend", result);
			$(result).appendTo(pictures);
			
			// depends on the browser, some IE versions use attachEvent()
			/*pictures.addEventListener('click', function(event) {
				
				var dreck = event.target.getAttribute("src");
				
				var choice = event.target.getAttribute("name");
				console.log(choice);
				
				document.getElementsByName("shopping_list_picture_id")[0].value = choice;

				document.getElementById("own-article-picture").setAttribute("src", dreck);

				return false;
			});*/
			
			pictures.on('click', function(event) {
				
				var picture = event.target.getAttribute("src");
				
				var choice = event.target.getAttribute("name");
				console.log(choice);
				
				//document.getElementsByName("shopping_list_picture_id")[0].value = choice;
				$("[name=shopping_list_picture_id]").val(choice);

				//document.getElementById("own-article-picture").setAttribute("src", dreck);
				$("#own-article-picture").attr("src", picture);
				
				$('[name=article_user_id_fk]').val("");

				return false;
			});
			
		}
		
		$('#dialog').dialog("open");
		
	});
	
	/*$(".search").keyup(function(e) {
		if (e.keyCode == KEYCODE_ESC){
			alert("getriggert");
		}
	});*/
	
	$(".search").focusout(function(){

		if($("#result").is(":visible")){
			$('#result').slideToggle('fast');
		}
	});
	
	$(".componentSearch").focusout(function(){

		if($("#result-components").is(":visible")){
			$('#result-components').slideToggle('fast');
		}
	});
	
	$(document).on("click", null, function(e){
		var $clicked = $(e.target);
		
		if (! $clicked.hasClass("search")){
			jQuery("#result").fadeOut();
		}
	});
	
	$('#searchid').click(function(){
		jQuery("#result").fadeIn();
	});
	
	$("#result").on("click", 'input', function(e){
		console.log("#result");
		var $clicked = $(e.target);
		var $name = $clicked.find('.name').html();
		var decoded = $("<div/>").html($name).text();
		$('#searchid').val(decoded);
	});


	
	$(".componentSearch").keyup(function(e){
		//console.log("Dreck");
		/*if (e.keyCode == 27){
			$("#third").focus();
		}*/
		
		var searchid = $(this).val();
		var dataString = 'search='+ searchid;
		
		if(searchid != ''){
			$.ajax({
				type: "POST",
				url: "http://192.168.178.41/ajax/component-search.php",
				data: dataString,
				cache: false,
				success: function(html){
					console.log(html);
					$("#result-components").html(html).show();
				}
			});
		}return false;
	});
	
	$(document).ready(function(){
		
		var article_image = "";
		var article_name = "";
		var article_description = "";
		
		var minValue = 0;
		var maxValue = 0;
		var valueCurrent = 0;
		var name = "";
		
		var fieldName = "";
		var type = "";
		
		var shopping_list_id = "";
		

		/* Date-Picker */
		$('.form_date').datetimepicker({
			language:  'de',
			weekStart: 1,
			todayBtn:  1,
			autoclose: 1,
			todayHighlight: 1,
			startView: 2,
			minView: 2,
			forceParse: 0
		});
		
		$("#dialog-confirm").dialog({
			resizable: false,
			autoOpen: false,
			height: "auto",
			width: 400,
			modal: true,
			buttons: {
			"OK": function(e) {
				//console.log(e.target.parentNode.parentNode.parentNode.getElementsByTagName("input")[0].value);
				var validation = "true";
				
				
				//var quantity = e.target.parentNode.parentNode.parentNode.getElementsByTagName("select").value;
				var quantity = $("[name=article_quantity]").val();
				//var quantity = document.getElementsByName("article_quantity")[0].value;
				//var list = document.getElementsByName("shopping_list")[0].value;
				var list = $("[name=shopping_list]").val();
				
				console.log(quantity.length);
				console.log($.isNumeric(quantity));
				console.log(list);
				console.log($.isNumeric(list));
				
				
				//console.log(typeof quantity);
				
				if(quantity.length > 0 && $.isNumeric(quantity) === false){
					console.log("A");
					$("[name=article_quantity]").css('border-color', 'red');
					validation = "false";
				}
				
				if(list.length > 0 && $.isNumeric(list) === false){
					console.log("B");
					$("[name=shopping_list]").css('border-color', 'red');
					validation = "false";
				}
				
				if(validation == "true"){
					console.log("Validation TRUE");
					addArticleList();
					$( this ).dialog( "close" );
				}
				
				return false;
				
			},
			Cancel: function() {
				$( this ).dialog( "close" );
				}
			}
		});
		
		//$("#dreck").on("click", function (){
			//$("#dialog").dialog();
		//});
		
		//var article_id = document.getElementsByName('article_user_id_fk')[0];
		var article_id = $('[name=article_user_id_fk]');
		
		//console.log(article_id.val());
		
		if(article_id.val() !== undefined){
			
			if (article_id.val() == ""){
				//document.getElementById('edit_shopping_list_name').style.visibility = "hidden";
				$('#edit_shopping_list_name').css({"visibility": "hidden"});
				
				//document.getElementsByName('shopping_list_name')[0].style.visibility = "hidden";
				$('[name=shopping_list_name]').css({"visibility": "hidden"});
				
			}else if(parseInt(article_id.val(), 10) !== 0){
				//document.getElementById('edit_shopping_list_name').style.visibility = "hidden";
				$('#edit_shopping_list_name').css({"visibility": "hidden"});
				
				
				//document.getElementsByName('shopping_list_name')[0].style.visibility = "hidden";
				$('[name=shopping_list_name]').css({"visibility": "hidden"});
				
				//var picture_id = document.getElementsByName('article_picture_id')[0].value;
				var picture_id = $('[name=article_picture_id]').val();
				
				getArticleInfos(article_id.val());
				
				//document.getElementsByClassName('media-heading')[0].textContent = article_name;
				$('.media-heading').text(article_name);
				
				//document.getElementsByClassName('media-description')[0].textContent = article_description;
				$('.media-description').text(article_description);
				
				//document.getElementsByName('shopping_list_name')[0].value = article_name;				
				$('[name=shopping_list_name]').text(article_name);				
				
				//document.getElementById('article-picture').setAttribute('src', 'http://192.168.178.41/uploads/article/' + picture_id + '/' + article_image + '_thumbnail.png');
				$('#article-picture').attr('src', 'http://192.168.178.41/uploads/article/' + picture_id + '/' + article_image + '_thumbnail.png');
			
			}else{
				
				var user_id = document.getElementsByName("user_id_fk")[0].value;
				var article_upload_name = document.getElementById("article-upload-name").innerText;
				//var shopping_list_id = document.getElementsByName("shopping_list_id")[0].value;
				var shopping_list_id = document.getElementsByName("shopping_list_picture_id")[0].value;
				
				if(shopping_list_id != 0){
					document.getElementById('own-article-picture').setAttribute('src', "http://192.168.178.41/uploads/article_upload/user_id_" + user_id + "/shopping_list_id_" + shopping_list_id + "/" + article_upload_name);
				}
				
				
				
				/*var user_id = document.getElementsByName("user_id_fk")[0].value;
				var shopping_list_id = document.getElementsByName("shopping_list_id")[0].value;;

				// View-Picture
				document.getElementById('own-article-picture').setAttribute('src', "http://192.168.178.41/uploads/article_upload/user_id_" + user_id + "/shopping_list_id_" + shopping_list_id + "/" + shopping_list_id + ".png");*/
			}
		}
		
		/*if(typeof document.getElementById("article-picture") != null && document.getElementById("article-picture").getAttribute("src") == "0"){
			if(typeof document.getElementById("user_id_fk_1") !== null && document.getElementById("shopping_list_id") !== null){
				if(document.getElementById("user_id_fk_1") != null && document.getElementById("shopping_list_id") != null){
					var user_id = document.getElementById("user_id_fk_1").value;
					var shopping_list_id = document.getElementById("shopping_list_id").innerHTML;

					// View-Picture
					document.getElementById('article-picture').setAttribute('src', "http://192.168.178.41/uploads/article_upload/user_id_" + user_id + "/shopping_list_id_" + shopping_list_id + "/" + shopping_list_id + ".png");
				}
			}
		}else{
			if(typeof article_id !== 'undefined'){

				if(article_id.value != "" && parseInt(article_id.value, 10) !== 0){
					console.log("JAAA");
					document.getElementById('edit_shopping_list_name').style.visibility = "hidden";
					document.getElementsByName('shopping_list_name')[0].style.visibility = "hidden";

					document.getElementById('article-picture').setAttribute('src', 'http://192.168.178.41/uploads/article/' + article_id.value + '/' + getArticle(article_id) + '_thumbnail.png');
				}
			}
		}*/
		
		$(".tab_article_1").on('click', function(){
			//document.getElementById('edit_shopping_list_name').style.visibility = "hidden";
			$('#edit_shopping_list_name').css({"visibility": "hidden"});
			
			//document.getElementsByName('shopping_list_name')[0].style.visibility = "hidden";
			$('[name=shopping_list_name]').css({"visibility": "hidden"});
			
			//document.getElementsByName('shopping_list_name')[0].value = "";
			$('[name=shopping_list_name]').val("");
		});
		
		$(".tab_article_2").on('click', function(){
			//$("[name=shopping_list_id]").val();
			//if(document.getElementsByName("shopping_list_id")[0].value != ""){
			if($("[name=shopping_list_id]").val() != ""){
				
				//document.getElementById('edit_shopping_list_name').style.visibility = "visible";
				$('#edit_shopping_list_name').css({"visibility": "visible"});
				
				//document.getElementsByName('shopping_list_name')[0].style.visibility = "visible";
				$('[name=shopping_list_name]').css({"visibility": "visible"});
			}
		});
		
		//console.log(document.getElementById('article-picture'));
		
		$("#result").on("click", function(e){
			console.log("#result");
			
			var targetSelect = "";
			var link = "";
			
			//if(document.getElementById('article-picture') != null){
			if($('#article-picture')[0] != undefined){
				
				e.preventDefault();
				
				var target = e.target.parentElement;
				//var targetSelect = e.target.children;
				console.log(target);
				//console.log(targetSelect);
				
				if(e.target.parentNode.parentElement.getAttribute("href") !== null){
					targetSelect = e.target.parentNode.parentElement.getAttribute("href");
					//console.log("A");
					//console.log(e.target.parentNode.parentElement);
					targetSelect = e.target.parentNode.parentElement;

				}else{
					targetSelect = e.target.parentElement.getAttribute("href");
					//console.log("B");
					//console.log(e.target.parentElement);
					targetSelect = e.target.parentElement;
				}
			
				// Picture Dropdown
				//var picture = document.getElementsByClassName('show')[0].getElementsByTagName('img')[0].getAttribute('src');
				var picture = targetSelect.getElementsByTagName("img")[0].getAttribute("src");
				//console.log(picture);
				

				// Name Dropdown
				//var name = document.getElementsByClassName('show')[0].getElementsByClassName('name')[0].innerText;
				var name = targetSelect.firstChild.children[1].innerText;
				//console.log(name);

				// Description Dropdown
				//var description = document.getElementsByClassName('show')[0].getElementsByClassName('description')[0].innerText;
				var description = targetSelect.firstChild.children[3].innerText;
				//console.log(description);

				//document.getElementById('article-picture').setAttribute("src", picture);
				$('#article-picture').attr("src", picture);
				//console.log(picture);
				
				
				//document.getElementsByClassName('media-heading')[0].textContent = name;
				$('.media-heading').text(name);
				//document.getElementsByClassName('media-description')[0].textContent = description;
				$('.media-description').text(description);
				//document.getElementsByName('shopping_list_name')[0].value = name;
				$('[name=shopping_list_name]').val(name);
				
				if(e.target.parentNode.parentElement.getAttribute("href") !== null){
					link = targetSelect.getAttribute("href");

				}else{
					link = targetSelect.getAttribute("href");
				}
				
				
				if(link !== null && $('[name=article_user_id_fk]').val() !== undefined){
					console.log(link);
					article_id.val(getArticleId(link));
				}
				//console.log(article_id.value);
				
				return false;
				
			}
			
			//return false;
			
		});
		
		var options = []; // Feld wo die Filter gespeichert werden

		$( '.pagina a' ).on( 'click', function( event ) {

		   var $target = $( event.currentTarget ),
			   val = $target.attr( 'data-value' ),
			   $inp = $target.find( 'input' ),
			   idx;

		   if ( ( idx = options.indexOf( val ) ) > -1 ) {
			  options.splice( idx, 1 );
			  setTimeout( function() { $inp.prop( 'checked', false ); }, 0);
		   } else {
			  options.push( val );
			  setTimeout( function() { $inp.prop( 'checked', true ); }, 0);
		   }

		   $( event.target ).blur();

		   console.log( options );
		   return false;
		});
		
		// Übergibt die Filter für die Artikelsuche an die var options beim jquery-ready
		if($(".pagina li").length > 0){
			console.log(".pagina li");
			var i = 0;
			while(i <= $(".pagina li").length -1){
				if ($('input[type="checkbox"]')[i].checked === true){
					options.push($(".pagina li a")[i].getAttribute("data-value"));
				}
				i++;
			}
		}
		
		$(".add-cart").on("click", function(){
			console.log("add-cart");
			options = [];
			
			
			//var user_id = document.getElementsByClassName("user_id")[0].innerText;
			var user_id = $(".user_id")[0].innerText;
			//var article_id = document.getElementsByClassName("article_id")[0].innerText;
			var article_id = $(".article_id")[0].innerText;
			
			if($(".pagina-radio li").length > 0){
				var i = 0;
				while(i <= $(".pagina-radio li").length -1){
					if ($('input[type="radio"]')[i].checked === true){
						options.push($(".pagina-radio li a")[i].getAttribute("data-value"));
					}
					i++;
				}
			}
			
			if(options !== ""){
				if($(".no-cart").is(":visible") == true){
					$(".no-cart").addClass("hiding");
				}
			}
			
			//console.log(options);
			//var article_count = document.getElementsByClassName("input-number")[0].value;
			var article_count = $(".input-number")[0].value;
			if(valueCurrent <= maxValue){
				if(user_id != null && article_id != null && article_count != null && options[0] != null){

					var dataString = "user=" + user_id + "&article=" + article_id + "&count=" + article_count + "&list=" + options[0];

					$.ajax({
						type: "POST",
						url: "http://192.168.178.41/ajax/add-article.php",
						data: dataString,
						cache: false,
						success: function(html){
							console.log(html);
							if(html == "success"){
								if($(".article-cart").is(":visible") == false){
									$(".article-cart").removeClass("hiding");
								}
							}
							//$("#result-components").html(html).show();
						}
					});

				}else{
					$(".no-cart").removeClass("hiding");
				}
			}
			
			return false;
			
			
			
		});
		
		$(".add-favos").on("click", function(){
			console.log("add-favos");

			//var user_id = document.getElementsByClassName("user_id")[0].innerText;
			var user_id = $(".user_id")[0].innerText;
			//var article_id = document.getElementsByClassName("article_id")[0].innerText;
			var article_id = $(".article_id")[0].innerText;
			
			console.log(user_id);
			console.log(article_id);
			
			if(user_id != null && article_id != null){

				var dataString = "user=" + user_id + "&article=" + article_id;

				$.ajax({
					type: "POST",
					url: "http://192.168.178.41/ajax/add-article.php",
					data: dataString,
					cache: false,
					success: function(html){
						console.log(html);
						if(html == "success"){
								if($(".article-favorites").is(":visible") == false){
									$(".article-favorites").removeClass("hiding");
								}
						}
						//$("#result-components").html(html).show();
					}
				});

				
				
			}
			return false;
		
		});
		
		$(".search").keyup(function(e){
			if (e.keyCode == 27){
				$("#third").focus();
			}

			var searchid = $(this).val();
			var radio = $("input[name='numbers']:checked").attr("id");
			var filter = options;
			var dataString = 'search='+ searchid + '&radio='+ radio + '&filter=' + filter;

			if(searchid != ''){
				$.ajax({
					type: "POST",
					url: "http://192.168.178.41/ajax/search.php",
					data: dataString,
					cache: false,
					success: function(html){
						$("#result").html(html).show();
					}
				});
			}return false;
		});
		
		$("#result-components").on("click", function(e){
			
			var component_id = "";
			
			//var id = document.getElementsByClassName('show')[0].getElementsByClassName('id')[0].innerText;
			//console.log(e.target.getElementsByClassName('id')[0]);
			if(typeof e.target.getElementsByClassName('id')[0] !== 'undefined'){
				//console.log(e.target.getElementsByClassName('id')[0].innerText);
				component_id = e.target.getElementsByClassName('id')[0].innerText;
				//document.getElementsByName("components_id_fk")[0].value = component_id;
				$("[name=components_id_fk]").text(component_id);
			}else{
				//console.log(e.target.parentElement.getElementsByClassName('id')[0].innerText);
				component_id = e.target.parentElement.getElementsByClassName('id')[0].innerText;
				//document.getElementsByName("components_id_fk")[0].value = component_id;
				$("[name=components_id_fk]").text(component_id);
			}
			
			return false;
		});
		
		function getArticleInfos(id){
			console.log(id);
			var jInfos = JSON.parse(getArticle(id));
			article_image = jInfos.article_image;
			article_name = jInfos.article_name;
			article_description = jInfos.article_description;
		}
		
		if(article_id.val() !== undefined){
			//if(parseInt(article_id.val(), 10) == 0 && $("[name=shopping_list_picture_id]").val() == ""){
			if($('[name=article_user_id_fk]').val() == "" && $("[name=shopping_list_picture_id]").val() == ""){
				$('.tab_article_1').addClass('active');
			}else if(parseInt($('[name=article_user_id_fk]').val(), 10) == 0 && parseInt($("[name=shopping_list_picture_id]").val(), 10) == 0){
				$('.tab_article_1').addClass('active');
			}else if(parseInt($("[name=shopping_list_picture_id]").val(), 10) != 0){
				$('.tab_article_2').addClass('active');
			}
		}
		
		/* Article Anzahl */
		$('.btn-number').click(function(e){
			e.preventDefault();

			fieldName = $(this).attr('data-field');
			type      = $(this).attr('data-type');
			var input = $("input[name='"+fieldName+"']");
			var currentVal = parseInt(input.val());
			if (!isNaN(currentVal)) {
				if(type == 'minus') {

					if(currentVal > input.attr('min')) {
						input.val(currentVal - 1).change();
					} 
					if(parseInt(input.val()) == input.attr('min')) {
						$(this).attr('disabled', true);
					}

				} else if(type == 'plus') {

					if(currentVal < input.attr('max')) {
						input.val(currentVal + 1).change();
					}
					if(parseInt(input.val()) == input.attr('max')) {
						$(this).attr('disabled', true);
					}

				}
			} else {
				input.val(0);
			}
		});
	
		$('.input-number').focusin(function(){
		   $(this).data('oldValue', $(this).val());
		});
	
		$('.input-number').change(function() {

			minValue =  parseInt($(this).attr('min'));
			maxValue =  parseInt($(this).attr('max'));
			valueCurrent = parseInt($(this).val());

			name = $(this).attr('name');
			if(valueCurrent >= minValue) {
				$(".btn-number[data-type='minus'][data-field='"+name+"']").removeAttr('disabled');
			} else {
				alert('Sorry, the minimum value was reached');
				$(this).val($(this).data('oldValue'));
			}
			if(valueCurrent <= maxValue) {
				$(".btn-number[data-type='plus'][data-field='"+name+"']").removeAttr('disabled');
			} else {
				alert('Sorry, the maximum value was reached');
				$(this).val($(this).data('oldValue'));
			}


		});
	
		$(".input-number").keydown(function (e) {
				// Allow: backspace, delete, tab, escape, enter and .
				if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
					 // Allow: Ctrl+A
					(e.keyCode == 65 && e.ctrlKey === true) || 
					 // Allow: home, end, left, right
					(e.keyCode >= 35 && e.keyCode <= 39)) {
						 // let it happen, don't do anything
						 return;
				}
				// Ensure that it is a number and stop the keypress
				if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
					e.preventDefault();
				}
		});
		/* Article Anzahl ENDE */
		
		
		/*$(".list-remove").on("click", function(e){
			console.log("list-remove");
			
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
				e.target.parentElement.parentNode.remove();
			}
			
			/*if(window.location.href == "http://192.168.178.41/site/login"){
				window.location.href = "http://192.168.178.41/";
			}else{
				location.reload();
			}*/
			/*return false;
		});*/
		
		/*$(".list-move").on("click", function(e){
			console.log("list-move");
			
			//console.log(e.target.parentElement.parentNode.childNodes);
			
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
			
			/*if(user_id != null && article_id != null && list_id != null){

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
			}*/
			
			//console.log($(".cd-cart-items"));
			
			//if(response == "success"){
				//console.log(e.target.parentElement.parentNode.innerHTML);
				//$(".cd-cart-items")[0].innerHTML = e.target.parentElement.parentNode.innerHTML;
				//e.target.parentElement.parentNode.remove();
			//}
			
			/*if(window.location.href == "http://192.168.178.41/site/login"){
				window.location.href = "http://192.168.178.41/";
			}else{
				location.reload();
			}*/
			
			//return false;
		/*});*/
		
		

		
		
		
		
		/*$(".cart-move").on("click", function(e){
			console.log("cart-move");
			
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
				e.target.parentElement.parentNode.remove();
			}
			
			/*if(window.location.href == "http://192.168.178.41/site/login"){
				window.location.href = "http://192.168.178.41/";
			}else{
				location.reload();
			}*/
			//return false;
		/*});*/
		
		/*$(".cart-checkout").on("click", function(e){
			console.log("cart-checkout");
			
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
				e.target.parentElement.parentNode.remove();
			}
			
			/*if(window.location.href == "http://192.168.178.41/site/login"){
				window.location.href = "http://192.168.178.41/";
			}else{
				location.reload();
			}*/
			//return false;
		/*});*/
		
		/*$(".cart-total-checkout").on("click", function(e){
			console.log("cart-total-checkout");
			
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
				$("[name=cd-cart-items]")[0].innerHTML = "";
			}
			
			/*if(window.location.href == "http://192.168.178.41/site/login"){
				window.location.href = "http://192.168.178.41/";
			}else{
				location.reload();
			}*/
			//return false;
		/*});*/
		
		$(".stock-new").on("click", function(e){
			console.log("stock-new");
			
			shopping_list_id = e.target.parentNode.parentNode.children[1].childNodes[1].firstElementChild.children[0].value;
			
			$('#dialog-confirm').dialog("open");
		});
		
		$(".stock-delete").on("click", function(e){
			console.log("stock-delete");
			
			//var user_id = document.getElementsByName("user_id")[0].value;
			var user_id = $("[name=user_id]").val();
			var shopping_list_id = e.target.parentNode.parentNode.getElementsByTagName("input")[0].value;
			var response = "";
			
			//console.log(user_id);
			//console.log(shopping_list_id);
			
			if(user_id != null && shopping_list_id != null){

				var dataString = "user=" + user_id + "&list_id=" + shopping_list_id;

				$.ajax({
					type: "POST",
					url: "http://192.168.178.41/ajax/manage-cart.php",
					data: dataString,
					cache: false,
					async: false,
					success: function(html){
						console.log(html);
						response = html;
					}
				});
			}
			
			if(response == "success"){
				e.target.parentNode.parentNode.remove();
			}
			
			//return false;
		});
		
		
		function addArticleList(){
			console.log("addArticleList");

			var shopping_id = shopping_list_id;
			//var quantity = document.getElementsByName("article_quantity")[0].value;
			var quantity = $("[name=article_quantity]").val();
			//var shopping_list = document.getElementsByName("shopping_list")[0].value;
			var shopping_list = $("[name=shopping_list]").val();
			var response = "";


			console.log(shopping_id);
			console.log(quantity);
			console.log(shopping_list);

			if(shopping_id != null && quantity != null && shopping_list != null){

				var dataString = "shopping_id=" + shopping_id + "&quantity=" + quantity + "&list=" + shopping_list;

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
				if(window.location.href == "http://192.168.178.41/site/login"){
					window.location.href = "http://192.168.178.41/";
				}else{
					location.reload();
				}
			}

		}

	
		var panels = $('.user-infos');
		var panelsButton = $('.dropdown-user');
		//panels.hide();

		//Click dropdown
		panelsButton.click(function() {
			//get data-for attribute
			var dataFor = $(this).attr('data-for');
			var idFor = $(dataFor);

			//current button
			var currentButton = $(this);
			idFor.slideToggle(400, function() {
				//Completed slidetoggle
				if(idFor.is(':visible'))
				{
					currentButton.html('<i class="glyphicon glyphicon-chevron-up text-muted"></i>');
				}
				else
				{
					currentButton.html('<i class="glyphicon glyphicon-chevron-down text-muted"></i>');
				}
			})
		});

		$('[data-toggle="tooltip"]').tooltip();
		
		if(document.getElementById("btnSearchFish") !=  null){
			//document.getElementById("btnSearchFish").addEventListener("click", function(){
			$("#btnSearchFish").on("click", function(){

				//var general_id = document.getElementById("fish_general").value;
				var general_id = $("#fish_general").val();
				//var lang = document.getElementsByTagName("html")[0].getAttribute("lang");
				var lang = $("html").attr("lang");

				$.ajax({
					url: "http://192.168.178.41/ajax/get-fish.php?fish_general_id=" + general_id + "&lang=" + lang,
					method: "GET",
					async: true,
					success: function(data, state, res){
						console.log(data);
						//var article_result = document.getElementById("article-result");
						var article_result = $("#article-result");
						article_result[0].innerHTML = data;

					},
					error: function(data, state){
						console.log(data);
					}
				});
			});
		}
		
		if($("#btnSearchLot").val() !=  undefined){
			//document.getElementById("btnSearchLot").addEventListener("click", function(){
			$("#btnSearchLot").on("click", function(){
				
				
				console.log("btnSearchLot");

				var aEntries = new Array();
				//var lotNr = document.getElementById("lot_nr").value; //L097RBA2A -- 29699100
				var lotNr = $("#lot_nr").val(); //L097RBA2A -- 29699100
				//var gitIn = document.getElementsByName("gitIn")[0].value;
				var gitIn = $("[name=gitIn]").val();
				//var dataString = "01=" + gitIn + "&10=" + lotNr;
				//document.getElementById("article-origin-result").innerHTML = "<div class='loader hiding'></div>";
				$("#article-origin-result")[0].innerHTML = "<div class='loader hiding'></div>";

				// url: "http://www.ftrace.com/de/de/productframe/epcis/" + gitIn + "/" + lotNr + "/edekafish-dev",

				if(lotNr != ""){
					$.ajax({
						url: "http://www.ftrace.com/de/de/productframe/epcis/" + gitIn + "/" + lotNr + "/edekafish-dev",
						method: "GET",
						async: true,
						beforeSend: function(){
							//document.getElementsByClassName("loader")[0].classList.remove("hiding");
							$(".loader").removeClass("hiding");
						},
						success: function(data, state, res){

							var parser = new DOMParser();
							var htmlDoc = parser.parseFromString(data, "text/html");
							//document.getElementById("container").innerHTML = data;

							var content = htmlDoc.getElementsByClassName("product_meta_table")[0];

							//console.log(content.innerHTML);

							//document.getElementById("container").innerHTML = content.innerHTML;

							//console.log(typeof content.children[0]);

							if(typeof content.children[0] != "undefined"){

								for(var i=0; i<=content.children[0].childElementCount; i+=2){

								var sTitle = content.children[0].childNodes[i].children[0].textContent;
								var sValue = content.children[0].childNodes[i].children[1].textContent.trim();

								var aEntry = [sTitle, sValue];

								aEntries.push(aEntry);
								//console.log(i);

								}
								console.log(aEntries);
								setFContent(aEntries);
							}else{
								sContent = "<div class='alert alert-danger' role='alert'>Not Found</div>";
								$("#article-origin-result")[0].innerHTML = sContent;
							}


						},
						error: function(data, state){
							console.log(data);
						}
					});
				}	
			});
		}

		function setFContent(aEntries){
			console.log("setFContent");
			
			var sContent = "<table  class='table'>";
			for(var i=1; i<=aEntries.length; i++){
			//console.log(aEntries[i-1][0]);
			sContent += "<tr><td>" + aEntries[i-1][0] + "</td><td>" + aEntries[i-1][1] + "</td></tr>";
			}

			sContent += "</table>"
			$("#article-origin-result")[0].innerHTML = sContent;

		}
		
		var classname = $(".slider");

		if(classname != null){
			var myFunction = function(e) {
				//var attribute = this.getAttribute("data-myattribute");
				//console.log(e.target.parentNode.parentNode.parentNode.children[0].innerText);
				
				setFilter(e.target.parentNode.parentNode.parentNode.children[0].innerText, !e.target.previousElementSibling.checked);
				//console.log(!e.target.previousElementSibling.checked);
				//document.getElementsByTagName("input")[0].value = "on"
			};

			for (var i = 0; i < classname.length; i++) {
				classname[i].addEventListener('click', myFunction, false);
			}
		}
		
		function setFilter(sFilter, bChecked){
			
			$("#global_user_id")[0].innerText;
			
			var dataString = "user_id=" + $("#global_user_id")[0].innerText + "&sFilter=" + sFilter + "&bChecked=" + bChecked;
			
			$.ajax({
				url: "http://192.168.178.41/ajax/add-filter.php",
				data: dataString,
				method: "GET",
				async: true,
				success: function(data, state, res){
					console.log(data);
				},
				error: function(data, state){
					console.log(data);
				}
			});
		}
		
		if($(".switch-filters")[0] != undefined){
			getFilter();
		}
		
		function getFilter(){
			//console.log("getFilter");
			
			var dataString = "user_id=" + document.getElementById("global_user_id").innerText;
			
			$.ajax({
				url: "http://192.168.178.41/ajax/add-filter.php",
				data: dataString,
				method: "GET",
				async: true,
				success: function(data, state, res){
					console.log(data);
					jsonData = JSON.parse(data);
					for(var i = 1; i<= Object.keys(jsonData).length; i++){
						setFilterNumber(Object.keys(jsonData).length, Object.keys(jsonData[i]));
						
					}
				},
				error: function(data, state){
					console.log(data);
				}
			});
		}
		
		function setFilterNumber(iLength, iNumber){
			var iKey = classname.length - iNumber;
			classname[iKey].previousElementSibling.checked = true;
		}
		
		if($(".article_id")[0] != undefined && $("#global_user_id") != undefined){
			//console.log(document.getElementsByClassName("article_id")[0].innerText);
			
			var user_id = $("#global_user_id").text();
			var article_id = $(".article_id").text();
			
			var dataString = "user_id=" + user_id + "&article_id=" + article_id;
			
			$.ajax({
				url: "http://192.168.178.41/ajax/get-allergene.php",
				data: dataString,
				method: "GET",
				async: true,
				success: function(data, state, res){
					console.log(data);
					var jsonData = JSON.parse(data);
					
					if(jsonData.length > 0){
						$(".article-alert").removeClass("hiding");

						var message = "";
						for(var i=1; i<=jsonData.length; i++){
							message += jsonData[i-1] + "<br />";
						}
						$(".article-alert")[0].innerHTML += message;
					}
				
				
				},
				error: function(data, state){
					console.log(data);
				}
			});
		}
		
	
	});/* JQUERY-READY ENDE */
	
	function getArticle(article_id){
		
		console.log("getArticle");
		console.log(article_id);
		
		var article = $.ajax({
			type: "POST",
			url: "http://192.168.178.41/ajax/article_search.php",
			data: "article_id=" + article_id,
			async: false,
			cache: false
		}).responseText;
		console.log(article);
		return article;
	}
	
	function getArticleId(path){
		console.log("getArticleId");
		var link = path;
		var sReplace = "http://192.168.178.41/site/list_articles/article/";
		var article = link.replace(sReplace, "");
		//console.log(article);
		return article;
	}
	
	function validateEmail(mail){
		var atpos = mail.indexOf("@");
		var dotpos = mail.lastIndexOf(".");
		if (atpos<1 || dotpos<atpos+2 || dotpos+2>=mail.length) {
			return false;
		}else{
			return true;
		}
	} 
	

	

});