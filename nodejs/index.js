const http = require("http"), url = require("url");

var admin = require("firebase-admin");
var mysql = require('mysql');
const hostname = "localhost";
const port = 3000;

var serviceAccount = require('./serviceAccountKey.json');


var connection = mysql.createConnection({
	host: 'localhost',
	user: 'root',
	password: 'root',
	database: 'barcode2'
});

connection.connect();

/*connection.query('SELECT * FROM bc_apikeys', function (error, results, fields) {
	if (error) throw error;
	console.log('The solution is: ', results[0].apikeys_key);
});*/



var firebase = admin.initializeApp({
	credential: admin.credential.cert(serviceAccount),
	databaseURL: "https://mybucket-f1d6a.firebaseio.com"
});

// Get a reference to the database service
  var database = firebase.database();

const server = http.createServer((req, res) => {
	res.statusCode = 200;
	res.setHeader("Content-type", "text/plain");
	//var query = url.parse(req.url, true).query;
	//getAuthUid(query.token, res);
	console.log("Build-Server");
	res.end();
});

//var verifiedUid = "";

server.listen(port, hostname, () => {
	setListenersLists();
	setListenersList();
	console.log("Server started on port " + port);
});

function setListenersLists(){
	console.log("setListenersLists");
	//console.log(uid);
	var userIdBackup = ''; // set Listener once per UserKey
	
	firebase.database().ref('shopping_lists/').on('child_added', function(postSnapshot) {
		//console.log(postSnapshot);
		
		var postReference = postSnapshot.ref;
		//var uid = postSnapshot.val().uid;
		var postId = postSnapshot.key;
		
		
		//console.log("ANDYYYY " + postId);
		
		// ADD
		firebase.database().ref('shopping_lists/' + postId).on('child_added', function(dataSnapshot) {
			
			console.log("DRECK DRECK");
			
			var postId2 = dataSnapshot.key;
			//console.log("GREGOR " + postReference);
			//console.log("GREGOR " + postId2);
			//console.log('The solution is: ', dataSnapshot.val().shoppingListsName);
			console.log("------------");
			console.log(postId);
			console.log(postId2);
			console.log("------------");
			
			if(userIdBackup != postId){
				userIdBackup = postId;
				setChangeListener(postId, postId2);
			}
			
			
			
			connection.query("SELECT * FROM bc_shopping_lists WHERE firebase_id = '" + postId2 + "'", function (error, results, fields) {
				//console.log(results.length);
				if (error) throw error;
				
				if(results.length == 1){
					console.log("Shopping-Lists DRIN :" + dataSnapshot.val().shoppingListsName);
				}else{
					console.log("NICHT DRIN");
					connection.query("INSERT INTO `bc_shopping_lists`(`shopping_lists_name`, `user_id_fk`, `shopping_lists_bought`, `firebase_id`) VALUES ('" + dataSnapshot.val().shoppingListsName + "','" + dataSnapshot.val().userId + "', " + dataSnapshot.val().shoppingListsBought + ", '" +  postId2 + "')", function (error, results, fields) {
						console.log("Shopping-Lists Added :" + dataSnapshot.val().shoppingListsName);
					});
				}
			});
			
			//SELECT * FROM bc_shopping_lists WHERE firebase_id = ''
			//INSERT INTO `bc_shopping_lists`(`shopping_lists_name`, `user_id_fk`, `shopping_lists_bought`, `firebase_id`) VALUES ([value-1],[value-2],[value-3],[value-4],[value-5])
			
		});
		
		// DELETE
		firebase.database().ref('shopping_lists/' + postId).on('child_removed', function(dataSnapshot) {
			
			var postId2 = dataSnapshot.key;
			
			connection.query("DELETE FROM bc_shopping_lists WHERE firebase_id = '" + postId2 + "'", function (error, results, fields) {
				//console.log(results.length);
				if (error) throw error;
			});	
		
		});
			
	});
	
	<!-- uid=Benutzernummer | uid2=Listnummer -->
	function setChangeListener(uid, uid2){

		firebase.database().ref('shopping_lists/' + uid + '/').on('child_changed', function(dataSnapshot) {
			var changedPost = dataSnapshot.val();

			//console.log("Source: " + changedPost);
			console.log("Ganser: " + changedPost.shoppingListsBought);
			console.log("Ganser: " + changedPost.shoppingListsKey);
			console.log("Ganser: " + changedPost.shoppingListsName);
			console.log("Ganser: " + changedPost.userId);
			
			console.log('shopping_lists/' + uid + '/' + uid2);
			
			// CHANGED
			firebase.database().ref('shopping_lists/' + uid).on('child_changed', function(dataSnapshot2) {
				//console.log(dataSnapshot.val());
				console.log("Schrang. " + dataSnapshot2.val());
				
				console.log("SELECT * FROM bc_shopping_lists WHERE firebase_id = '" + dataSnapshot2.val().shoppingListsKey + "'");
				
				connection.query("SELECT * FROM bc_shopping_lists WHERE firebase_id = '" + dataSnapshot2.val().shoppingListsKey + "'", function (error, results, fields) {
					console.log(results.length);
					if (error) throw error;
					
					if(results.length == 1){
						
						console.log("Shopping-Lists DRIN :" + dataSnapshot2.val().shoppingListsName);
						console.log("Statement: " + " UPDATE `bc_shopping_lists` SET `shopping_lists_name`='" + dataSnapshot2.val().shoppingListsName + "',`user_id_fk`='" + dataSnapshot2.val().userId + "',`shopping_lists_bought`=" + dataSnapshot2.val().shoppingListsBought + " WHERE `firebase_id`='" + dataSnapshot2.val().shoppingListsKey + "'");
						
						connection.query("UPDATE `bc_shopping_lists` SET `shopping_lists_name`='" + dataSnapshot2.val().shoppingListsName + "',`user_id_fk`='" + dataSnapshot2.val().userId + "',`shopping_lists_bought`=" + dataSnapshot2.val().shoppingListsBought.toString() + " WHERE `firebase_id`='" +  dataSnapshot2.val().shoppingListsKey + "';", function (error, results) {
							if (error) throw error;
							//console.log("ERROR : " + error);
							console.log(results.affectedRows + " record(s) updated");
							console.log("Shopping-Lists Updated :" + dataSnapshot2.val().shoppingListsName);
							//console.log("UPDATE `bc_shopping_lists` SET `shopping_lists_name`='" + dataSnapshot2.val() + "',`user_id_fk`='" + dataSnapshot2.val().userId + "',`shopping_lists_bought`=" + dataSnapshot2.val().shoppingListsBought + " WHERE `firebase_id`='" +  dataSnapshot2.val().shoppingListsKey + "'");
						});
						
					}else{
						
						console.log("NICHT DRIN");
						connection.query("INSERT INTO `bc_shopping_lists`(`shopping_lists_name`, `user_id_fk`, `shopping_lists_bought`, `firebase_id`) VALUES ('" + dataSnapshot2.val().shoppingListsName + "','" + dataSnapshot2.val().userId + "', " + dataSnapshot2.val().shoppingListsBought + ", '" +  dataSnapshot2.val().shoppingListsKey + "')", function (error, results) {
							console.log("Shopping-Lists Added :" + dataSnapshot2.val());
						});
						
					}
					
				});
				
			});
		
		});
	
	}
	
}

function setListenersList(){
	console.log("setListenersList");
	var userIdBackup = ''; // set Listener once per UserKey
	
	firebase.database().ref('shopping_list/').on('child_added', function(postSnapshot) {
		//console.log(postSnapshot);
		
		var postReference = postSnapshot.ref;
		var postId = postSnapshot.key;
		
		console.log("+++++++++++++++++");

		//console.log(postId);
		var key = Object.keys(postSnapshot.val())[0];
		var key2 = Object.keys(postSnapshot.child(key).val())[0];
		//console.log(postSnapshot.child(key).child(key2).val());
		//console.log(postSnapshot.child(postSnapshot.val().key).val());
		//console.log(postSnapshot.child('-LDT6kRU9bP8mYIBW3XT').key);
		//console.log(postSnapshot.child('-LDT6kRU9bP8mYIBW3XT').child('-LDTFkHce4Q9I4qVNs4i').val());
		
		console.log("+++++++++++++++++");
		
		
		
		// ADD
		firebase.database().ref('shopping_list/' + postId + "/" + key + "/").on('child_added', function(dataSnapshot) {
			
			console.log("ADD SHOPPING_LIST");
			
			//var postId2 = dataSnapshot.key; // Liste-Key
			
			console.log("------------");
			console.log(dataSnapshot.val());
			console.log(dataSnapshot.val().shoppingListKey);
			console.log("------------");
			
			/*if(userIdBackup != postId){
				userIdBackup = postId;
				setChangeListener(postId, postId2);
			}*/
			
			
			
			connection.query("SELECT * FROM bc_shopping_list WHERE firebase_id = '" + dataSnapshot.val().shoppingListKey + "'", function (error, results, fields) {
				//console.log(results.length);
				if (error) throw error;
				
				if(results.length == 1){
					console.log("Shopping-List DRIN :" + dataSnapshot.val().shoppingListName);
				}else{
					console.log("NICHT DRIN");
					
					//connection.query("INSERT INTO `bc_shopping_list`(`shopping_lists_name`, `user_id_fk`, `shopping_lists_bought`, `firebase_id`) VALUES ('" + dataSnapshot.val().shoppingListsName + "','" + dataSnapshot.val().userId + "', " + dataSnapshot.val().shoppingListsBought + ", '" +  postId2 + "')", function (error, results, fields) {																																																																
					console.log("INSERT INTO `bc_shopping_list`(`user_id_fk`, `article_user_id_fk`, `shopping_list_picture_id`, `article_search`, `shopping_list_name`, `shopping_list_quantity`, `unit_id_fk`, `shopping_list_durability`, `shopping_list_bought`, `place_id_fk`, `firebase_id`) VALUES ('" + dataSnapshot.val().userId + "', " + dataSnapshot.val().articleId + ", '', NULL,'" + dataSnapshot.val().shoppingListName + "', " + dataSnapshot.val().shoppingListQuantity + ", " + dataSnapshot.val().unitId+1 + ", '" + dataSnapshot.val().shoppingListDurability + "', " + dataSnapshot.val().shoppingListBought + ", " + dataSnapshot.val().placeId + ", '" + dataSnapshot.val().shoppingListKey + "')");
					connection.query("INSERT INTO `bc_shopping_list`(`user_id_fk`, `article_user_id_fk`, `shopping_list_picture_id`, `article_search`, `shopping_list_name`, `shopping_list_quantity`, `unit_id_fk`, `shopping_list_durability`, `shopping_list_bought`, `place_id_fk`, `firebase_id`) VALUES ('" + dataSnapshot.val().userId + "', " + dataSnapshot.val().articleId + ", '', NULL,'" + dataSnapshot.val().shoppingListName + "', " + dataSnapshot.val().shoppingListQuantity + ", " + dataSnapshot.val().unitId+1 + ", '" + dataSnapshot.val().shoppingListDurability + "', " + dataSnapshot.val().shoppingListBought + ", " + dataSnapshot.val().placeId + ", '" + dataSnapshot.val().shoppingListKey + "')", function (error, results, fields) {
						console.log("Shopping-Lists Added :" + dataSnapshot.val().shoppingListName);
					});
				}
			});
			
		});
	
	});
}

/*http.createServer(function (req, res) {
    var query = url.parse(req.url,true).query;
    res.end(JSON.stringify(query));
}).listen(3333);*/