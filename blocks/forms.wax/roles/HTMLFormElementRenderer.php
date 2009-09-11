<?php
	interface HTMLFormElementRenderer extends View {}
	
	class HTMLFormElementRendererActions {
		static function attr2str(HTMLFormElementRenderer $self, $attribs) {
			$tmp = array();
			foreach ($attribs as $var => $val)
				$tmp[] = "$var='$val'";
			$attribute_str = implode(" ",$tmp);
			return $attribute_str;
		}
		static function RenderInput(HTMLFormElementRenderer $self, $type, $name, $value = NULL, $attribs = NULL) {
			$attribs = (is_null($attribs) ? array() : $attribs);
			
			$attribs['name'] = $name;
			$attribs['type'] = $type;
			$attribs['value'] = $value;
			
			$block = Wax::GetBlock("forms");
			return $self->Render($block->views('input'),array("attributes" => $self->attr2str($attribs)));
		}
		
		function TextField(HTMLFormElementRenderer $self, $name, $value = NULL, $attribs = NULL) {
			return $self->RenderInput('text', $name, $value, $attribs);
		}
		function PasswordField(HTMLFormElementRenderer $self, $name,$attribs = NULL) {
			return $self->RenderInput('password', $name, NULL, $attribs);
		}
		function HiddenField(HTMLFormElementRenderer $self, $name, $value = NULL, $attribs = NULL) {
			return $self->RenderInput('hidden', $name, $value, $attribs);
		}
		function SubmitButton(HTMLFormElementRenderer $self, $value = NULL, $attribs = NULL) {
			return $self->RenderInput('submit', '', $value, $attribs);
		}
		function ResetButton(HTMLFormElementRenderer $self, $value = NULL, $attribs = NULL) {
			return $self->RenderInput('reset', '', $value, $attribs);
		}
		function FileField(HTMLFormElementRenderer $self, $name, $attribs = NULL) {
			return $self->RenderInput('file', $name, NULL, $attribs);
		}


		function Textarea(HTMLFormElementRenderer $self, $name, $value = NULL, $attribs = NULL) { 
			$attribs = (is_null($attribs) ? array() : $attribs);
			$attribs['name'] = $name;
			$block = Wax::GetBlock("forms");
			return $self->Render($block->views("textarea"),array("value" => $value, "attributes" => $self->attr2str($attribs)));
		}
		
		function SelectMenu(HTMLFormElementRenderer $self, $name, $options, $selected = NULL, $attribs = NULL) {
			$attribs = (is_null($attribs) ? array() : $attribs);
			$attribs['name'] = $name;
			$block = Wax::GetBlock("forms");
			return $self->Render($block->views("select"),array("default_value" => $selected, "options" => $options, "attributes" => $self->attr2str($attribs)));
		}
	}
?>