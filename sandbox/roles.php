<?php
	require_once "../wax_init.php";				// initialize the framework
	
	// Load the Hello World Block
	// Wax::LoadBlock("helloworld");
	
	// Manually define the Hello World role here
	// define a role
	interface rHelloWorld {}
	
	class rHelloWorldActions {
		// defines the 'message' role property
		var $message = "Hello World...";
		
		// defines the 'Hello' role method
		static function Hello(rHelloWorld $self) {
			echo "{$self->message}\n";
		}
	}
	
	
	// implement a role
	class Foo extends DCIObject implements rHelloWorld {
		// set the role property here
		var $message = "Hello from Foo!";
	}
	class Bar extends DCIObject implements rHelloWorld {
		var $message = "Hello from Bar!";
	}
	
	$foo = new Foo();
	$foo->Hello();
	
	$bar = new Bar();
	$bar->Hello();
?>