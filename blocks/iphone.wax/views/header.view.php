<html>
	<head>
		<title><?=$title?></title>
		<meta id="viewport" name="viewport" content="width=320;initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
		
		<?php foreach ($css as $css_address): ?>
		<link rel='stylesheet' type='text/css' href='<?=$css_address?>' />
		<?php endforeach; ?>
			
		<?php foreach ($js as $script_address): ?>
		<script type='text/javascript' src='<?=$script_address?>'></script>
		<?php endforeach; ?>
	</head>
	<body>
	
	