(function() {
	

	$(document).ready(function(){
	
	// Initialize Firebase
	var config = {
		apiKey: "AIzaSyDyCvq-Psi1MNjKAJkZx_0x_X0rUqqwXkw",
		authDomain: "mybucket-f1d6a.firebaseapp.com",
		databaseURL: "https://mybucket-f1d6a.firebaseio.com",
		projectId: "mybucket-f1d6a",
		storageBucket: "mybucket-f1d6a.appspot.com",
		messagingSenderId: "653876229069"
	};
	firebase.initializeApp(config);
		
	if(document.getElementById("firebaseui-auth-container") != null){
		
		// FirebaseUI config.
		var uiConfig = {
			callbacks: {
			  signInSuccess: function(currentUser){ return false; },
			},
			signInOptions: [
			// Leave the lines as is for the providers you want to offer your users.
			firebase.auth.GoogleAuthProvider.PROVIDER_ID,
			firebase.auth.TwitterAuthProvider.PROVIDER_ID,
			],
			// Terms of service url.
			tosUrl: '<your-tos-url>'
		};
		// Initialize the FirebaseUI Widget using Firebase.
		var ui = new firebaseui.auth.AuthUI(firebase.auth());
		// The start method will wait until the DOM is loaded.
		ui.start('#firebaseui-auth-container', uiConfig);
		console.log("UI");
		console.log(ui);
	}
	
	//console.log(firebase);
	var btnSessionLogout = document.getElementById("btnSessionLogout");
	//var btnSessionLogout = $("#btnSessionLogout");
		
		
	//const txtEmail = document.getElementById("username");
	const txtEmail = $("#username");
	
	//const txtPassword = document.getElementById("password");
	const txtPassword = $("#password");
		
	//const btnLogin = document.getElementById("btnLogin");
	const btnLogin = $("#btnLogin");
	
	
	//const txtLogin = document.getElementById("txtLogin");	
	

	//const txtPassword_2 = document.getElementById("password_2");
	const txtPassword_2 = $("#password_2");
	//const btnSignUp = document.getElementById("btnSignUp");	
	const btnSignUp = $("#btnSignUp");	
		
		
		
	//const txtLogout = document.getElementById("txtLogout");
	
		
	if(btnLogin != null){
		/*btnLogin.addEventListener("click", e => {

			console.log("btnLogin");

			//Get email and pass
			const email = txtEmail.value;
			const pass = txtPassword.value;
			const auth = firebase.auth();

			// Sign in
			const promise = auth.signInWithEmailAndPassword(email, pass);
			promise.catch(e => getLoginMessage(e.message));

		});*/
	
		btnLogin.on("click", e => {

			console.log("btnLogin");

			//Get email and pass
			const email = txtEmail.val();
			const pass = txtPassword.val();
			const auth = firebase.auth();

			// Sign in
			const promise = auth.signInWithEmailAndPassword(email, pass);
			promise.catch(e => getLoginMessage(e.message));

		});
	}
		
	function getLoginMessage(message){
		console.log(message);
		if(message == "There is no user record corresponding to this identifier. The user may have been deleted."){
			//document.getElementsByClassName("user-not-found")[0].classList.remove("hiding");
			$(".user-not-found").removeClass("hiding");
		}else if(message == "The password is invalid or the user does not have a password."){
			//document.getElementsByClassName("user-pass-wrong")[0].classList.remove("hiding");
			$(".user-pass-wrong").removeClass("hiding");
		}
	}	
		
	function setSignInMessage(e){
		if(e.message == "The email address is already in use by another account."){
			//document.getElementsByClassName("email-use")[0].classList.remove("hiding");
			$(".email-use")[0].removeClass("hiding");
			//document.getElementsByClassName("password-chars")[0].classList.add("hiding");
			$(".password-chars").addClass("hiding");
		}else if(e.message == "Password should be at least 6 characters"){
			//document.getElementsByClassName("password-chars")[0].classList.remove("hiding");
			$(".password-chars").removeClass("hiding");
			
			//document.getElementsByClassName("email-use")[0].classList.add("hiding");
			$(".email-use").addClass("hiding");
		}else{
			console.log(e.message);
		}
	}
	
	function setUserID(currentUser, idToken){
		console.log("setUserID");
		console.log(currentUser);
		console.log("Token: " + idToken);
		console.log("UID: " + currentUser.uid);

		console.log("emailVerified:" + currentUser.emailVerified);
		console.log("photoURL:" + currentUser.photoURL);

		var name = "";
		if(currentUser.displayName == null){
			name = currentUser.email;
		}else{
			name = currentUser.displayName;
		}
		
		var displayName = "";
		console.log(currentUser);
		if(typeof currentUser.screenName != "undefined"){
			displayName = currentUser.screenName;
		}else{
			displayName = name;
		}

		/*console.log("displayName:" + name);
		console.log("email:" + currentUser.email);*/

		//document.getElementsByName("user_token")[0].value = idToken;
		$("[name=user_token]").val(idToken);
		
		//document.getElementsByName("user_screenname")[0].value = displayName;
		$("[name=user_screenname]").val(displayName);
		
		//document.getElementsByName("user_name")[0].value = name;
		$("[name=user_name]").val(name);
		
		//document.getElementsByName("user_email")[0].value = currentUser.email;
		$("[name=user_email]").val(currentUser.email);
		
		//document.getElementsByName("user_emailVerified")[0].value = currentUser.emailVerified;
		$("[name=user_emailVerified]").val(currentUser.emailVerified);
		
		//document.getElementsByName("user_photo")[0].value = currentUser.photoURL;
		$("[name=user_photo]").val(currentUser.photoURL);
		
		console.log("Submit-triggerd");
		console.log(window.location.href);
		$('#form-login').trigger('submit');
	}
	
	/*function validateMyForm(){
		consol.log("DUMM");
		return false;
	}*/
	
	/*$('#form').submit(function (evt) {
    	evt.preventDefault();
		console.log("Form-Submit");
	});*/

	if(btnSignUp != null){
		/*btnSignUp.addEventListener("click", e => {
			console.log("btnSignUp");

			//Get email and pass
			const email = txtEmail.value;
			const pass = txtPassword.value;
			const pass2 = txtPassword_2.value;
			const auth = firebase.auth();

			// Sign in
			const promise = auth.createUserWithEmailAndPassword(email, pass).then(function(){
				var user = firebase.auth().currentUser;
				console.log(user);
				document.getElementsByClassName("acc-created")[0].classList.remove("hiding");
				document.getElementsByClassName("signup")[0].classList.add("hiding");
				document.getElementsByClassName("auth")[0].classList.remove("hiding");
				
				document.getElementsByClassName("auth")[0].addEventListener("click", function(){
					window.location.href = "http://localhost/bootstrap/site/login";
				});
				
			}).catch(e => setSignInMessage(e));
			
		});*/
	
		btnSignUp.on("click", e => {
			console.log("btnSignUp");

			//Get email and pass
			const email = txtEmail.val();
			const pass = txtPassword.val();
			const pass2 = txtPassword_2.val();
			const auth = firebase.auth();

			// Sign in
			const promise = auth.createUserWithEmailAndPassword(email, pass).then(function(){
				var user = firebase.auth().currentUser;
				console.log(user);
				//document.getElementsByClassName("acc-created")[0].classList.remove("hiding");
				$(".email-use").addClass("hiding");
				$(".password-chars").addClass("hiding");
				$(".acc-created").removeClass("hiding");
				
				//document.getElementsByClassName("signup")[0].classList.add("hiding");
				$(".signup").addClass("hiding");
				
				
				//document.getElementsByClassName("auth")[0].classList.remove("hiding");
				$(".auth").removeClass("hiding");
				
				$(".auth").on("click", function(){
					window.location.href = "http://localhost/bootstrap/site/login";
				});
				
			}).catch(e => setSignInMessage(e));
			
		});	
	
	}
	
	/*btnLogout.addEventListener("click", e => {
		firebase.auth().signOut();
	});*/
	
	firebase.auth().onAuthStateChanged(firebaseUser => {
		console.log("onAuthStateChanged");
		
		if(firebaseUser){
			//console.log(firebaseUser);
		
			firebaseUser.getIdToken(/* forceRefresh */ true).then(function(idToken) {
			  // Send token to your backend via HTTPS
			  // ...
				if($("#form-login")[0] != undefined){
					setUserID(firebaseUser, idToken);
				}

			});
			
		}else{
			console.log("Not logged in");
		}
		
		
		/*if(btnSessionLogout !== null){
			console.log("btnSessionLogout");
			btnSessionLogout.addEventListener("click", e => {
				console.log("btnSessionLogout");
				try{
					firebase.auth().signOut();
					console.log("Not logged in");
				}catch(err){
					console.log(err);
				}
				
				return false;
			});
		}*/
		
		if(btnSessionLogout !== null){
			console.log("btnSessionLogout");
			btnSessionLogout.addEventListener("click", e => {
				console.log("btnSessionLogout");
				try{
					firebase.auth().signOut();
					console.log("Not logged in");
				}catch(err){
					console.log(err);
				}
				
				return false;
			});
		}

		
	});
	
		
	});
	
}());