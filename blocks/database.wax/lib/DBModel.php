<?php
	class DBModel extends Model implements DBCRUD,View {
		var $pk = "id";
		var $id = NULL;
		var $data = array();
		
		var $table_prefix = "";
		var $column_prefix = "";
		
		
		var $reflection = NULL;
		
		function __construct($id = NULL) {
			parent::__construct();
			
			$this->id = $id;
			$this->reflection = $this->Reflect();
		}
	}
?>