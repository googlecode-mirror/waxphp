<?php
    interface rRouter {
        function GetAliases();
    }
    
    class rRouterActions {
        /**
        * Analyzes a querystring into an array of arguments.
        * The urlparts array supplies index aliasing
        *
        * @param string $querystring The querystring to analyze
        */
        static function Analyze(rRouter $self, $querystring) {
            $aliases = $self->GetAliases();
			$url_parts = explode("/",$querystring);
			
			// parse the URL parts into named parameters
    		foreach ($url_parts as $index => $piece) {
			    $expanded = explode(":",$piece);

				if (count($expanded) > 1) {
				    $index = $expanded[0];
				    $piece = $expanded[1];
				}
				else if (isset($aliases[$index])) {
				    $index = $aliases[$index];
				}

				$route[$index] = $piece;
			}
			return $route;
        }
    
        /**
        * Complement to the Analyze() method, this method 
        * generates a querystring that can be analyzed
        * by Analyze().
        *
        * @param array $args The array of arguments to turn into a querystring
        */
        static function Create(rRouter $self, $args) {
            $qsparts = array();
            foreach ($args as $name => $value) {
                if (isset($this->urlparts[$name])) {
                    $qsparts[] = $value;
                }
                else {
                    $qsparts[] = "$name:$value";
                }
            }
            return implode("/",$qsparts);
        }
    }
?>