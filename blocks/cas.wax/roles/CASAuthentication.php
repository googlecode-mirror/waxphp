<?php
	interface rCASAuthentication extends Role {}
	
	class rCASAuthenticationActions {
		static function Authenticate(rCASAuthentication $self) {
			phpCAS::client(CAS_VERSION_2_0,'login.rpi.edu',443,'/cas',false);
	        phpCAS::setNoCasServerValidation();
	        phpCAS::forceAuthentication();

	        $user = phpCAS::getUser();
	        return isset($user);
		}
		static function GetUser(rCASAuthentication $self) {
			return phpCAS::getUser();
		}
	}
?>