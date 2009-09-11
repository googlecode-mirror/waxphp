<?php
	// exception classes for DCI
	class DCIException extends Exception {}				// generic DCI exception class
	
	class UnknownModelException extends DCIException {}
	class UnknownActionException extends DCIException {}
	class UnknownContextException extends DCIException {}
	
	class UnknownRoleException extends DCIException {}	// when a model tries to act as a role that doesn't exist
?>