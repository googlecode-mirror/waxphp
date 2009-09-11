<?php
	class HeaderController extends DCIObject implements View {
		function __construct($title) {
			parent::__construct(); // initialize parent- need to for injection
			
			$args = BlockManager::GetDHTMLResources();
			$args['title'] = $title;
			
			$block = Wax::GetBlock("iphone");
			echo $this->Render($block->views('header'), $args);
		}
	}
?>