<?php
	require_once "../../../wax_init.php";
	
	$block = Wax::LoadBlock("messageboxes");
	
	class TestController extends Plugin implements MessageBoxAlerter {
		function __construct() {
			parent::__construct();
			
			$this->Success("Success Message","Hello... this is a test success message...");
			$this->Error("Error Message","Hello... this is a test error  message...");
			$this->Warning("Warning Message","Hello... this is a test warning message...");
			$this->Info("Info Message","Hello... this is a test info message...");
		}
	}
?>
<link rel='stylesheet' type='text/css' href='<?=$block->css("messageboxes")?>' />
<?php new TestController(); ?>