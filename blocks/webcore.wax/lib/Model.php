<?php
	// the base persistence model class
	// forms a base for models that communicate with databases, 
	// specifically couchdb
	class PersistenceModel extends ArraySurrogate {
		private $_initial_keys = array();
		function __construct($id = null) {
			$array = array();
			parent::__construct($array);
			
			if (!is_null($id) && $this->TryTo("Read"))
				$this->_arrayref = $this->Read($id);
				
			if (!isset($this->_arrayref['type']))
				$this->_arrayref['type'] = $this->type();
			
			// call an initialization function if it exists
			try {
				$this->Init();
			}
			catch (MethodNotFoundException $mnfe) {}
		}
		function update($data) {
			$this->_arrayref = $data;
		}
		function type() {
			$base = get_class($this);
			$base = str_replace("Model","",$base);
			return strtolower($base);
		}
		function __destruct() {
			/*
			if ($this->TryTo("Save")) {
				$this->Save($this->_arrayref);
			}
			*/
		}
	}
?>