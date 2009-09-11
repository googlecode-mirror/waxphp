<?php
	interface CASAuthentication extends Role {}
	
	class CASAuthenticationActions {
		static function Authenticate() {
			phpCAS::client(CAS_VERSION_2_0,'login.rpi.edu',443,'/cas',false);
	        phpCAS::setNoCasServerValidation();
	        phpCAS::forceAuthentication();

	        $user = phpCAS::getUser();
	        return isset($user);
		}
		static function GetUser() {
			return phpCAS::getUser();
		}
	}
?>