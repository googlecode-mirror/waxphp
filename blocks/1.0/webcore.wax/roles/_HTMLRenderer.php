<?php
	interface rHTMLRenderer extends rRole {}
	
	class rHTMLRendererActions {
		static function attr2str(rHTMLFormElementRenderer $self, $attribs) {
			$tmp = array();
			foreach ($attribs as $var => $val)
				$tmp[] = "$var='$val'";
			$attribute_str = implode(" ",$tmp);
			return $attribute_str;
		}
		static function RenderInput(rHTMLFormElementRenderer $self, $type, $name, $value = NULL, $attribs = NULL) {
			$attribs = (is_null($attribs) ? array() : $attribs);
			
			$attribs['name'] = $name;
			$attribs['type'] = $type;
			$attribs['value'] = $value;
			
			$block = Wax::GetBlockFromContext(__FILE__);
			return $self->Render($block->views('HTML/input'),array("attributes" => $self->attr2str($attribs)));
		}
		
		function TextField(rHTMLFormElementRenderer $self, $name, $value = NULL, $attribs = NULL) {
			return $self->RenderInput('text', $name, $value, $attribs);
		}
		function PasswordField(rHTMLFormElementRenderer $self, $name,$attribs = NULL) {
			return $self->RenderInput('password', $name, NULL, $attribs);
		}
		function HiddenField(rHTMLFormElementRenderer $self, $name, $value = NULL, $attribs = NULL) {
			return $self->RenderInput('hidden', $name, $value, $attribs);
		}
		function SubmitButton(rHTMLFormElementRenderer $self, $value = NULL, $attribs = NULL) {
			return $self->RenderInput('submit', '', $value, $attribs);
		}
		function ResetButton(rHTMLFormElementRenderer $self, $value = NULL, $attribs = NULL) {
			return $self->RenderInput('reset', '', $value, $attribs);
		}
		function FileField(rHTMLFormElementRenderer $self, $name, $attribs = NULL) {
			return $self->RenderInput('file', $name, NULL, $attribs);
		}


		function Textarea(rHTMLFormElementRenderer $self, $name, $value = NULL, $attribs = NULL) { 
			$attribs = (is_null($attribs) ? array() : $attribs);
			$attribs['name'] = $name;
			$block = Wax::GetBlock("forms");
			return $self->Render($block->views("textarea"),array("value" => $value, "attributes" => $self->attr2str($attribs)));
		}
		
		function SelectMenu(rHTMLFormElementRenderer $self, $name, $options, $selected = NULL, $attribs = NULL) {
			$attribs = (is_null($attribs) ? array() : $attribs);
			$attribs['name'] = $name;
			$block = Wax::GetBlock("forms");
			return $self->Render($block->views("select"),array("default_value" => $selected, "options" => $options, "attributes" => $self->attr2str($attribs)));
		}
	}
?>