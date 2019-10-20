<?php

include "vendor/autoload.php";

use Firebase\Auth\Token\Verifier;

class Proof {
	
	
	private $projectId = "mybucket-f1d6a";
	private $verifiedIdToken;
	//$idTokenString = strval($_GET["token"]);
	
	function __construct(){
		
	}
	
	function getProof($idTokenString = "", $variant = ""){
		
		// in der App Variante soll ein return erfolgen und in der Web ein echo
		//echo $variant;
		
		if($idTokenString == ""){
			$idTokenString = 'eyJhbGciOiJSUzI1NiIsImtpZCI6ImIyOGM3MzNhY2Y3YTcyNTg0ZmUxMTg4MGJjMmFkNDhkYTIxZTQ1OTEifQ.eyJpc3MiOiJodHRwczovL3NlY3VyZXRva2VuLmdvb2dsZS5jb20vbXlidWNrZXQtZjFkNmEiLCJhdWQiOiJteWJ1Y2tldC1mMWQ2YSIsImF1dGhfdGltZSI6MTUxNTUwMDQzMCwidXNlcl9pZCI6IkllVFZHamJiY2hjYWQzaENxMzBKWmcxZlRKajEiLCJzdWIiOiJJZVRWR2piYmNoY2FkM2hDcTMwSlpnMWZUSmoxIiwiaWF0IjoxNTE1NTAwNTg1LCJleHAiOjE1MTU1MDQxODUsImVtYWlsIjoiY29vbGVyQHRlc3QuZGUiLCJlbWFpbF92ZXJpZmllZCI6ZmFsc2UsImZpcmViYXNlIjp7ImlkZW50aXRpZXMiOnsiZW1haWwiOlsiY29vbGVyQHRlc3QuZGUiXX0sInNpZ25faW5fcHJvdmlkZXIiOiJwYXNzd29yZCJ9fQ.ZPPKmGsrUOGKtxBZfceUzQAnYeEwUY-1djgbSv6aCObqbukWdMICGLnWF3yHUJ5vOn6r6SE7EEc4qXzuaTR-gjjF18caZkEQEJaSR_rcBO6pau6FrAj4Acy695ngYJokp1mDSwLRXk323X4C1vGVjrkA8YrFchr-8S2oZ85xGKMTre778wRXYNNSXrGCnDKLlZPCxF-KIeMWmXUCaImeEJUquG5WlKzvym5kf1g95MD7tFjsUumUSEsHEUd3nKES3eYevgN2eKvn-6YD4fIkc6Yfg6An3igzpl4-Vl5-_31W5ZAaZjJHi-ELLnR_79Ru4AMz3q1KWoNn4JJ_33oICw';
		}
		
		//echo $idTokenString;
		
		$verifier = new Verifier($this->projectId);
		
		try {
			$verifiedIdToken = $verifier->verifyIdToken($idTokenString);

			return $verifiedIdToken->getClaim('sub'); // "a-uid"
		} catch (\Firebase\Auth\Token\Exception\ExpiredToken $e) {
			if($variant == "app"){
				return $e->getMessage();
			}else{
				echo $e->getMessage();	
			}
		} catch (\Firebase\Auth\Token\Exception\IssuedInTheFuture $e) {
			if($variant == "app"){
				return $e->getMessage();
			}else{
				echo $e->getMessage();	
			}
		} catch (\Firebase\Auth\Token\Exception\InvalidToken $e) {
			if($variant == "app"){
				return $e->getMessage();
			}else{
				echo $e->getMessage();	
			}
		} catch (\Firebase\Auth\Token\Exception\InvalidArgumentException $e){
			if($variant == "app"){
				return $e->getMessage();
			}else{
				echo $e->getMessage();	
			}
		}
		
		//return $verifiedIdToken->getClaim('sub');
		
	}

}

?>