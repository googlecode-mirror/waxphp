<?php
	require_once "rCouchDB.php";

	interface rCouchDBPersistenceModel extends rCouchDB {}
		
	class rCouchDBPersistenceModelActions {
		// role properties
		var $_id_alias;		// alias for _id, allows for stuff like making a post title the _id without explicity setting it.
		var $views;
		
		// role methods
		static function Read(rCouchDBPersistenceModel $self, $id = null) {
			if (is_null($id)) {
				$info = $self->ApplyCustomView("all_messages");
				
				foreach ($info as $id => $row) {
					$info[$id][$self->_id_alias] = $info[$id]['_id'];
				}
			}
			else {
				$info = $self->Query("GET","$id");
				
				if (isset($info["_id"]))
					$info[$self->_id_alias] = $info["_id"];
			}
			
			return $info;
		}
		
		static function Delete(rCouchDBPersistenceModel $self, $id, $revision) {
			$self->Query("DELETE","$id?rev=$revision");
		}
		
		static function Init(rCouchDBPersistenceModel $self) {
			if ($self->TryTo("Query")) {
				 if (!isset($self->_id_alias))
					$self->_id_alias = "_id";
			}
			else {
				error_log("ERROR: rCouchPersistenceModels rely on rCouchDB");
			}
		}
		
		static function GetCustomView(rCouchDBPersistenceModel $self,$name,$map,$reduce = null) {
			if ($self->TryTo("ApplyCustomView")) {
				$viewdata = $self->ApplyCustomView($name,$map,$reduce);
				$self->update($viewdata);
				
				return $viewdata;
			}
			else {
				error_log("ERROR (GetCustomView): rCouchDBPersistenceModel relies on rCouchDB");
			}
		}
		
		static function SaveView(rCouchDBPersistenceModel $self, $name, $map, $reduce = null) {
			$self->ApplyCustomView($name,$map,$reduce);
		}
		
		// insert / update a record
		static function Save(rCouchDBPersistenceModel $self) {
			if ($self->TryTo("ToArray")) {
				$data = $self->ToArray();
				
				if (isset($data[$self->_id_alias])) {
					$data['_id'] = $data[$self->_id_alias];
					unset($data[$self->_id_alias]);
				}
				if (!isset($data['type']))
					$data['type'] = $self->type();
				
				if (isset($data['_id']) && !empty($data['_id']))
					$self->Query("PUT",$data['_id'],$data);
				else
					$self->Query("POST","/",$data);
			}
			else {
				error_log("ERROR: rCouchPersistenceModels rely on PersistenceModel");
			}
		}
	}
	
?>