<?php
	// simple view controller: DCIObject implementing View
	class FooterController extends DCIObject implements rView {
		function __construct() {
			parent::__construct(); // initialize parent- need for injection
			
			$block = Wax::GetBlock("iphone");
			echo $this->Render($block->views('footer'), array());
		}
	}
?>