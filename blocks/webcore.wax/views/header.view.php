<html>
	<head>
		<title>Wax Sample Website</title>
		<?php foreach ($css as $css_address): ?>
		<link rel='stylesheet' type='text/css' href='<?=$css_address?>' />
		<?php endforeach; ?>
		
		
		<?php foreach ($js as $script_address): ?>
		<script type='text/javascript' src='<?=$script_address?>'></script>
		<?php endforeach; ?>
	</head>
	<body >
	
	<div class='pagewrapper'>
		<div class='header'>
			<div class='navigation'>
				<ul class='wnavlist'>
					<li><?php echo $self->Link("Home", array('controller' => 'Home')); ?></li>
					<li><?php echo $self->Link("Walkthrough", array('controller' => 'Walkthrough')); ?>
					<li><?php echo $self->Link("Runtime Info", array('controller' => 'Home', 'action' => 'runtime')); ?></li>
				</ul>
			</div>
		</div>
		<div class='content'>