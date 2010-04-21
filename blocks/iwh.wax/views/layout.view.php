<html>
	<head>
		<title>Insert Website Here</title>
		<?php foreach ($css as $css_address): ?>
		<link rel='stylesheet' type='text/css' href='<?=$css_address?>' />
		<?php endforeach; ?>
			
		<?php foreach ($js as $script_address): ?>
		<script type='text/javascript' src='<?=$script_address?>'></script>
		<?php endforeach; ?>
	</head>
	<body>
	
	<div class='pagewrapper'>
		<div class='header'>
		    <div class='container'>
                <h3 style='color:white;'>InsertWebsiteHere</h3>
            </div>
		</div>
		<div class='content'>
		    <?php echo $content_for_layout; ?>    
		</div>
    	<div class='footer'>
		    <div class='container'>
		        <div style='position:relative; float:left;'>
        		    <img src='<?php echo $block->images("logo_small"); ?>' />
        		</div>
        		
        		<div style='position:relative; float:right; margin-left:15px;'>
        		    <a href='http://code.google.com/p/waxphp'>
            		    <img src='<?php echo $block->images("wax_logo_small"); ?>' />
        		    </a>
        		</div>
        		<div style='position:relative; float:right'>
    	    	    <b>InsertWebsiteHere</b><br />
        		    &copy; <?php echo date("Y"); ?> Joe Chrzanowski<br  />
    		    
        		    Developed as a project for the 
        		    <a href='http://rcos.cs.rpi.edu/'>
        				RPI Center for Open-Source Software
        			</a><br />
        		</div>
    		</div>
		</div>
	</div>
	</body>
</html>