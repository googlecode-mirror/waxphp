<?php
	// empty class -- for when just the base role is needed
	// the View role is a great example.  sometimes, an object
	// that can render a view is needed:
	//  	ex:
	//			$renderer = new View();
	//			$renderer->Render(...);
	class View extends DCIObject implements rView {}
?>