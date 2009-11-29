<?php
	/**
	* The base class for Wax exceptions.  
	* 
	* This class is flexible to allow the display of exceptions 
	* depending on which blocks have been loaded.  By default looks
	* for a view named 'exception.view.php' in the resources block
	* or in the block set to WaxConf::$defaultResourceBlock
	*
	* @author Joe Chrzanowski <joechrz@gmail.com>
	*/
	class WaxException extends Exception {
		protected $title = "";
		protected $details = "";
		protected $code = 0;
		protected $file = '';
		protected $line = 0;
		
		/**
		* Default exception constructor.  Receives the title and a textual
		* representation of the details to display
		*
		* @param string $title The short description of the exception
		* @param mixed $details The long description or an array to display
		* @param int $code The error code for this exception
		*/
		function __construct($title, $details = "", $code = NULL, $file = NULL, $line = NULL) {
			$this->title = $title;
			$this->details = $details;
			$this->code = $code;
			
			$this->file = (is_null($file) ? $this->getFile() : $file);
			$this->line = (is_null($line) ? $this->getLine() : $line);
		}
		
		/**
		* This function returns the exception in a well-organized and readable way.
		* Uses the 'exception' view from the $defaultResourceBlock.  If the view
		* isn't found, it simply returns a textual representation
		*/
		function __toString() {
			try {
				// find default exception view:
				$block = BlockManager::GetBlock("messageboxes");
				$view = $block->views("exception");
				$renderer = DCIObject::Generate(array("rFlash","rView"));
				
				$msgbuf = "<i>" . $this->details . "</i><br /><br />";
				$msgbuf .= "An uncaught exception has occurred in: <br />";
				$msgbuf .= "<pre>";
				$trace = $this->getTrace();
				
				foreach ($trace as $tr) {
				    if (isset($tr['file']) && strpos($tr['file'],'DCIObject') === false) {
				        $msgbuf .= $tr['file'] . ":" . $tr['line'] . "<br />";
				    }
				}
                
			    $msgbuf .= "</pre>";
				
				$result = $renderer->RenderMessage('error',$this->title,$msgbuf);
			}
			catch (WaxException $vnfe) {
				// if no view found, print out simple text
				$this->details = (is_array($this->details) ? "<pre>" . print_r($this->details,true) . "</pre>": $this->details);
				
				$return = ($this->code > 0 ? "( " . $this->code . " ) " : "") . 
				        $this->file . ":" . $this->line . "<br />" . 
				        $this->title . (!empty($this->details) ? ": " . $this->details : "") . "<br /><br />" . 
				        "<b>Backtrace:</b><br />";
				foreach ($this->getTrace() as $tr) {
				    if (isset($tr['file']) && strpos($tr['file'],'DCIObject') === false) {
				        $return .= $tr['file'] . ":" . $tr['line'] . "<br />";
				    }
				}
				return $return;
			}
		}
	}
	
	function wax_error_handler($code, $message, $file, $line) {
	    @ob_end_flush();
	    throw new WaxException("Uncaught Exception",$message, $code, $file, $line);
	}
	
	function wax_exception_handler($exception) {
	    @ob_end_flush();
		try {
		    if (!($exception instanceof WaxException)) {
		        $exception = new WaxException($exception->getMessage(), "", $exception->getFile(), $exception->getLine());
		    }
			echo($exception->__toString());
		}
		catch (Exception $e) {
			die(get_class($e) . " thrown within the exception handler. <br />" .
			    $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "<br />");
		}
	}
?>