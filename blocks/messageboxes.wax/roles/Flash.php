<?php
	interface rFlash extends rView {}
	
	class rFlashActions {
		// main render function
		static function RenderMessage($self, $type, $title, $message, $return = false) {
			$block = BlockManager::GetBlockFromContext(__FILE__);
			
			$args = array(
				"title" => $title, 
				"message" => $message,
				"type" => $type,
				"image" => $block->images($type)
			);
			
			$viewfile = $block->views("message");
			$buf = $self->Render($viewfile, $args);
			
			if ($return) return $buf;
			else echo $buf;
		}
		
		// shortcut functions
		static function Success($self, $title, $message) {
			$self->RenderMessage('success',$title,$message);
		}
		static function Error($self, $title, $message) {
			$self->RenderMessage('error',$title,$message);
		}
		static function Warning($self, $title, $message) {
			$self->RenderMessage('warning',$title,$message);
		}
		static function Info($self, $title, $message) {
			$self->RenderMessage('info',$title,$message);
		}
	}
?>