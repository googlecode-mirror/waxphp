<?php
	class PathManager {
		// get a full path -- this function is responsible for any routing requests
        // -- it looks up paths for the entire wax framework, therefore,
        // the entire framework can be restructured by modifying only the path
        // variables above, then making the proper corresponding calls to this function
        static function LookupPath($what, $args = null) {
			$replaced = preg_replace("/([\w]+)/","[$0]",$what);
			
			$replaced = self::ResolvePaths($replaced);
			if (!is_null($args)) {
				$replaced = self::ResolveArgs($replaced,$args);
			}

			return preg_replace("/([\/]+)/","/",$replaced);
        }
        
        // make sure the paths are only getting parsed once per page load
        // we can probably cache this somewhere too
        static function PreParse() {
            foreach (WaxConf::$paths as $path => $parse) {
                WaxConf::$paths[$path] = self::ResolvePaths($parse);
            }
        }
        
        // a couple functions to switch between filesystem and web paths
        static function FStoWEB($path) {
        	$path = str_replace(self::LookupPath("fs"),self::LookupPath("web/"),$path);
        	return $path;
        }
        static function WEBtoFS($path) {
        	$path = self::LookupPath("DOCUMENT_ROOT") . $path;
        	return $path;
        }
        
        // resolve paths using other variabels from the $_paths array
        // also replace any <VAR> vars with $_SERVER[VAR]
        static function ResolvePaths($path) {
            $matches = array();
            
            // replace the paths with whatever
            while (preg_match_all('/\[(\w+)]/',$path, $matches)) {
                foreach ($matches[1] as $match) {
                	if (isset(WaxConf::$paths[$match]))
	                    $path = str_replace("[$match]",WaxConf::$paths[$match],$path);
	                else {
	                	echo "Error - Invalid variable: $match<br />\n";
	                	$path = str_replace("[$match]",'',$path);
	                }
                }
            }
           
			// replace <var> with definitions from $_SERVER
            preg_match_all('/<(\w+)>/',$path, $matches);
            foreach ($matches[1] as $match) {
                $path = str_replace("<$match>",$_SERVER[$match],$path);
            }
            
            return $path;
        }
        
        // get a path using specific arguments
        // example:
        //      ResolveArgs('image',array('package'=>'Alerts/Message', 'image' => 'error'));
        // returns the path to the error.png image relative to the Wax root folder
        static function ResolveArgs($path,$vars) {
            $matches = array();
            if (!is_array($vars)) return $path;
            
            foreach ($vars as $key => $val) {
                $path = str_replace("{" . $key . "}",$val,$path);
            }
            return $path;
        }
	}
?>