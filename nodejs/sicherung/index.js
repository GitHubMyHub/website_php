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

connection.query('SELECT * FROM bc_apikeys', function (error, results, fields) {
	if (error) throw error;
	console.log('The solution is: ', results[0].apikeys_key);
});



var firebase = admin.initializeApp({
	credential: admin.credential.cert(serviceAccount),
	databaseURL: "https://mybucket-f1d6a.firebaseio.com"
});

// Get a reference to the database service
  var database = firebase.database();

const server = http.createServer((req, res) => {
	res.statusCode = 200;
	res.setHeader("Content-type", "text/plain");
	var query = url.parse(req.url, true).query;
	//console.log(query.token);
	//console.log(getAuthUid(query.token));
	getAuthUid(query.token, res);
	res.end();
    //res.end(JSON.stringify(query));
});

//var verifiedUid = "";

server.listen(port, hostname, () => {
	console.log("Server started on port " + port);
});

function getAuthUid(idToken, res) {
	
	admin.auth().verifyIdToken(idToken)
		.then(function(decodedToken) {
			var uid = decodedToken.uid;
			//var obj = '{ "uid":' + uid + '}';
			//res.end(JSON.stringify(obj));
			//verifiedUid = uid;
			//setUid(uid);
			setListeners(uid);
		}).catch(function(error) {
			// Handle error
			console.log("Error: " + error);
	});
	
}

function setUid(uid){
	console.log("setUid");
	//console.log(uid);
	verifiedUid = uid;
}

function setListeners(uid){
	console.log("setListeners");
	console.log(uid);
	
	//firebase.database().ref('/posts').on('child_added', function(postSnapshot) {
	
	firebase.database().ref('shopping_lists/' + uid).on('child_added', function(postSnapshot) {
		//console.log(postSnapshot);
		
		var postReference = postSnapshot.ref;
		//var uid = postSnapshot.val().uid;
		var postId = postSnapshot.key;
		
		console.log("ANDYYYY " + postId);
		
		postReference.child(postId).on('value', function(dataSnapshot) {
			
		});
			
	});
	
	firebase.database().ref('shopping_lists/' + uid).on('child_changed', function(postSnapshot) {
		//console.log(postSnapshot);
		
		var postReference = postSnapshot.ref;
		//var uid = postSnapshot.val().uid;
		var postId = postSnapshot.key;
		
		console.log("FLOOOHHHH " + postId);
		
		postReference.child(postId).on('value', function(dataSnapshot) {
			
		});
			
	});
		
}


/*http.createServer(function (req, res) {
    var query = url.parse(req.url,true).query;
    res.end(JSON.stringify(query));
}).listen(3333);*/