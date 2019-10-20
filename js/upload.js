


jQuery(function($){
	
	"use strict";
	$(document).ready(function(){
		
		
		/* View */
		if(typeof document.getElementsByName("article_user_id_fk")[0] !== 'undefined' && document.getElementsByName("article_user_id_fk")[0].value == 0){
			/*var user_id = document.getElementsByName("user_id_fk")[0].value;
			var shopping_list_id = document.getElementsByName("shopping_list_id")[0].value;
			var article_picture_name = document.getElementById("article-upload-name").innerHTML;
			
			console.log("http://192.168.178.41/uploads/article_upload/user_id_" + user_id + "/shopping_list_id_" + shopping_list_id + "/" + article_picture_name);
			
			document.getElementById('own-article-picture').setAttribute('src', "http://192.168.178.41/uploads/article_upload/user_id_" + user_id + "/shopping_list_id_" + shopping_list_id + "/" + article_picture_name);*/
		}

		$(document).on('change', '#upload-file-selector', function(){

			var name = document.getElementById("upload-file-selector").files[0].name;
			var form_data = new FormData();
			var ext = name.split('.').pop().toLowerCase();
			
			if(jQuery.inArray(ext, ['gif','png','jpg','jpeg']) == -1){
				alert("Invalid Image File");
			}

			var f = document.getElementById("upload-file-selector").files[0];
			var fsize = f.size||f.fileSize;
			
			if(fsize > 2000000){
				alert("Image File Size is very big");
			}else{
				form_data.append("file", document.getElementById('upload-file-selector').files[0]);
				form_data.append("user_id", document.getElementsByName("user_id_fk")[0].value);
				form_data.append("shopping_list_id", document.getElementsByName("shopping_list_id")[0].value);
				
				$.ajax({
					url:"http://192.168.178.41/include/upload.php",
					method:"POST",
					data: form_data,
					contentType: false,
					cache: false,
					processData: false,
					beforeSend: function(){
						$('#upload-message').html("<div class='alert alert-success'><strong>Success!</strong> Image was successfully uploaded.</div>");
					},   
					success: function(data){
						console.log(data);
						$('#own-article-picture').attr("src", data);
						$('input[name=shopping_list_picture_id]').val($('input[name=shopping_list_id]').val()); // set own Article Picture
						var article_user_id = document.getElementsByName('article_user_id_fk')[0];
						article_user_id.value = 0;
					}
				});
			}
		});
	});	
});