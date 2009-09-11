<?php
	interface MessageBoxRenderer extends View {}
	
	class MessageBoxRendererActions {
		// main render function
		static function RenderMessage(MessageBoxRenderer $self, $type, $title, $message) {
			$block = Wax::GetBlock("messageboxes");
			$args = array(
				"title" => $title, 
				"message" => $message,
				"type" => $type . " no_image"
			);
			echo $self->Render($block->views("message"), $args);
		}
		
		// shortcut functions
		static function Success(MessageBoxRenderer $self, $title, $message) {
			$self->RenderMessage('success',$title,$message);
		}
		static function Error(MessageBoxRenderer $self, $title, $message) {
			$self->RenderMessage('error',$title,$message);
		}
		static function Warning(MessageBoxRenderer $self, $title, $message) {
			$self->RenderMessage('warning',$title,$message);
		}
		static function Info(MessageBoxRenderer $self, $title, $message) {
			$self->RenderMessage('info',$title,$message);
		}
	}
?>