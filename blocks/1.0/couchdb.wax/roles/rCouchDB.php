<?php
	// role definition
	interface rCouchDBDatabase {}
	interface rCouchDBDocument {}
	interface rCouchDBView {}
		
	// rCouchDB -- base functionality to connect to and manipulate data in a couch database
	interface rCouchDB extends rCouchDBDatabase, rCouchDBDocument, rCouchDBView {}
	class rCouchDBActions {
		// properties
		var $dbhost;
		var $dbport;
		var $dbname;
			
		// raw couch db request
		// returns a raw deserialized json object
		static function Query(rCouchDB $self, $method, $url, $data = NULL) {			
			if (is_null($self->dbhost)) $self->dbhost = "127.0.0.1";
			if (is_null($self->dbport)) $self->dbport = 5984;
			if (is_null($self->dbname)) error_log("Error: no database specified!");
			
			$url = "/" . $self->dbname . "/$url";
			
			$couchsrv = fsockopen($self->dbhost, $self->dbport);
			if ($couchsrv) {
				$buf = "$method $url HTTP/1.0\r\n";
				
				echo "<b>*** COUCHDB --&gt;</b> $buf<br />";
				if (is_array($data)) 
					echo "<pre style='margin-left:50px;'>" . print_r($data,true) . "</pre>";
				
				$buf .= "Host: " . $self->dbhost . "\r\n";
				
				if (is_array($data)) {
					$data = json_encode($data);
				}
				
				if (is_string($data)) {
					$buf .= "Content-Length: " . strlen($data) . "\r\n";
					$buf .= "Content-Type: application/json\r\n";
					$buf .= "\r\n";
					$buf .= $data . "\r\n";
				}
				
				$buf .= "\r\n\r\n";
					
				fwrite($couchsrv, $buf);
				
				$response = '';
				while (!feof($couchsrv)) {
					$response .= fgets($couchsrv);
				}
				fclose($couchsrv);
				
				// parse the response
				$response_data = substr($response,strpos($response,"\r\n\r\n"));
				$json_response = json_decode($response_data);
				
				$converted_result = $self->ConvertObjects($json_response);
				
				/*********** DEBUG *************/
				$response_code = array();
				preg_match_all("/(\d{3}) (\w+)/",array_shift(explode("\r\n",$response)),$response_code);
				$code = $response_code[1][0];
				$msg = $response_code[2][0];
				
				$headers = array_shift(explode("\r\n\r\n",$response));
				if ($code >= 400)
					echo "<span style='font-weight:bold;color:red;'>Error: </span><pre style='background:#FFDDDD;'>$headers\n\n" . print_r($converted_result,true) . "</pre>";
				else if ($code >= 200 && $code < 300)
					echo "<span style='font-weight:bold;color:green;'>Success: </span><pre style='background:#DDFFDD;'>$headers\n\n" . print_r($converted_result,true) . "</pre>";
			    /******************************/
				
				return $converted_result;
			}
			else {
				error_log("ERROR Connecting to couchDB server @ " . $self->dbname . ":" . $self->port);
			}
		}
		
		/**
		* This function takes a CouchDB response and converts it into
		* a simple array structure
		*/
		static function ConvertObjects(rCouchDB $self, $response) {			
			// converts a result set 
			if (isset($response->rows)) {
				$response_info = array();
				
				foreach ($response->rows as $doc) {
					$index = (isset($doc->key) ? $doc->key : $doc->_id);
					$response_info[$index] = array();
									
					$vars = get_object_vars($doc->value);
					foreach ($vars as $obvar => $value) {
						$response_info[$index][$obvar] = $value;
					}
				}
			}
			else if (isset($response->views)) {
				$response_info['views'] = array();
				foreach (get_object_vars($response->views) as $viewname => $funcs) {
					$response_info['views'][$viewname] = $funcs;
				}
			}
			// converts and individual row
			else {
				$response_info = get_object_vars($response);
			}
			return $response_info;
		}
		
		/**
		*	$conditions = array(
		*		array(
	    *			array(
		*				'field' => 'colname',
		*				'value'  => 'some value',
		*				'comparison' => '>'
		*			),
		*			"and",
		*			array(
		*				'field' => 'othercolname',
		*				'value' => 'another value',
		*				'comparison' => '.equals'
		*			)
		*		)
		*	);
		*
		*   Will return the view:
		*
		* 	function (doc) {
		*		if (doc.colname > 'some value' && doc.othercolname.equals('another value')) {
	 	*			emit(doc._id,doc);
		*		}
		* 	}
		*/
		private static function conditionsToJS(rCouchDB $self, $conditions) {
			$logical_comparisons = array('<','>','==','>=','<=','!=');
			
			$result = "(";
			$comparisons = array();
			foreach ($conditions as $condition) {
				if (is_array($condition)) {
					$buf = "doc." . $condition['field'];
					
					if (!is_numeric($condition['value'])) $condition['value'] = "'" . $condition['value'] . "'";
					
					// if it's a logical comparison
					if (array_search($condition['comparison'],$logical_comparisons)) {
						$buf .= ' ' . $condition['comparison'] . ' ' . $condition['value'];
					}
					// if its a functional boolean comparison
					else if ($condition['comparison'][0] == '.') {
						$buf .= substr($condition['comparison'],1) . '(';
						$buf .= $condition['value'];
						$buf .= ')';
					}
					$comparisons[] = $buf;
				}
				else {
					if ($condition == "and")
						$result .= " && ";
					else if ($condition == "or")
						$result .= " || ";
				}
			}
			$result .= implode(($type == "and" ? " && " : " || "),$comparisons);
			$result .= ")";
			return $result;
		}
		private static function getMapFunction(rCouchDB $self, $conditions) {
			$func = "function (doc) {";
			$func .= "if " . $self->conditionsToJS($conditions);
			$func .= "}";
		}
		
		static function ApplyCustomView(rCouchDB $self, $name, $map = null, $reduce = null) {
			$viewdoc = $self->Query("GET","_design/custom");
			if (isset($viewdoc['error'])) {
				$viewdoc = array("_id" => "_design/custom", "language" => "javascript", "views" => array());
			}
			if (!isset($viewdoc['views'][$name])) {
				
				echo "<pre>";
				print_r($self->views);
				echo "</pre>";
				
				if (isset($map)) {
					$viewdoc['views'][$name]["map"] = $map;
				}
				else if (isset($self->views[$name]['map'])) {
					$viewdoc['views'][$name]['map'] = $self->views[$name]['map'];
				}
			
				if (isset($reduce)) {
					$viewdoc['views'][$name]["reduce"] = $reduce;
				}
				else if (isset($self->views[$name]['reduce'])) {
					$viewdoc['views'][$name]['reduce'] = $self->views[$name]['reduce'];
				}
				
				$self->Query("PUT","_design/custom",$viewdoc);
			}
			
			// now get it as a real view again
			$viewresult = $self->Query("GET","_design/custom/_view/$name");
			return $viewresult;
		}
		static function FetchView(rCouchDB $self, $name) {
			$viewresult = $self->Query("GET","_design/custom/_view/$name");
			return $viewresult;
		}
	}
	
	//  REQUEST				  | DESCRIPTION							  | SPECIAL RETURNS
	//  -------------------------------------------------------------------------------
	/** Database API
	*	/_all_dbs				fetch all databases
	*	PUT /dbname/			create a database
	*	DELETE /dbname/			delete a database
	* 	GET /dbname/			get info about a database
	*/
	
	/** Document API
	*	GET /dbname/docid		get a document
	* 		rev=revid				document revision					
	*		revs=[true|false]		whether to get all revisions		_revisions
	*/
?>