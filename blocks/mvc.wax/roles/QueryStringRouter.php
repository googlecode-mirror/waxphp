<?php
	interface QueryStringRouter extends Role {}
	
	class QueryStringRouterActions {
		static function init(QueryStringRouter $self) {
			$self->Route();
		}
		static function Route(QueryStringRouter $self) {
			$parts = explode("/",$_SERVER["QUERY_STRING"]);
			foreach ($self->vars as $var) {
				$partindex = array_search($var,$self->vars);
				if (isset($parts[$partindex])) {
					$val = $parts[$partindex];
					
					$_GET[$var] = $val;
					$_REQUEST[$var] = $val;
				}
			}
		}
	}
?>