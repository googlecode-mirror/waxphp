<?php
	/**
	* These interfaces define the properties of classes (usually models)  that 
	* interact with the database.
	*/
	// define the DBUser role that contains utility functions for escaping and reflecting
	interface rDBUser extends Role {}
	
	// define the subroles of DBUser
	interface rDBCreator extends rDBUser {}
	interface rDBReader extends rDBUser {}
	interface rDBUpdater extends rDBUser {}
	interface rDBDeleter extends rDBUser {}
	
	// and finally define a SuperRole that can perform all the basic roles of a DBUser
	// PHP automatically figures out interface inheritance, so this will reflect how we expect
	interface rDBCRUD extends rDBUser, rDBCreator, rDBReader, rDBUpdater, rDBDeleter {}
	
	
	class rDBUserActions {
		static function Escape(rDBUser $self, $str) {
			if (is_numeric($str)) return $str;
			else return "'" . mysql_real_escape_string($str) . "'";
		}
		static function ParseTable(rDBUser $self) {
			$table = get_class($self);
			$table = str_replace(array("model","Model"), "", $table);
			return strtolower($self->table_prefix . $table);
		}
		static function Reflect(rDBUser $self) {
			$query = "SHOW COLUMNS FROM " . $self->ParseTable() . ";";
			$result = mysql_query($query) or die("ERROR: " . mysql_error());
			
			$fields = array();
			while ($row = mysql_fetch_assoc($result)) {
				$pieces = array();
				preg_match_all("/^(\w+)(\((\d+)\))*$/",$row['Type'],$pieces);
				$fieldinfo = array(
					'type' => $pieces[1],
					'size' => $pieces[2],
					'null' => $row['Null'],
					'keytype' => $row['Key'],
					'default' => $row['Default'],
					'extra' => $row['Extra']
				);

				$fields[$row['Field']] = $fieldinfo;
			}
			
			return $fields;
		}
		static function Query(rDBUser $self, $query) {
			$res = mysql_query($query) or die("ERROR: " . mysql_error());
			$ret = array();
			
			while ($row = mysql_fetch_assoc($res)) {
				$ret[] = $row;
			}
			
			return $ret;
		}
		static function Execute(rDBUser $self, $query) {
			mysql_query($query) or die("ERROR: " . mysql_error());
		}
	}
	
	// give the roles some actions -- just basic mysql actions for now
	class DBCreatorActions {
		static function Create(rDBCreator $self, array $arguments) {
			// grab the table name from $self->model
			$table = $self->ParseTable();
			$query = "INSERT INTO $table (";
			$cols = array();
			foreach (array_keys($arguments) as $key) {
				$cols[] = "`" . $self->column_prefix . "$key`";
			}
			$query .= implode(",",$cols);
			$query .= ") VALUES (";
			$values = array();
			foreach ($arguments as $value) {
				$values[] = $self->Escape($value);
			}
			$query .= implode(",",$values);
			$query .= ");";
						
			mysql_query($query) or die("ERROR: " . mysql_error());
			return mysql_insert_id();
		}
	}
	class DBReaderActions {
		static function Read(rDBReader $self, $arguments = NULL) {
			$table = $self->ParseTable();
			
			$query = "SELECT * FROM $table";
			$order = NULL;
			if (isset($arguments['sort'])) {
				$order = $arguments['sort'];
				$order['column'] = $self->column_prefix . $order['column'];
				unset($arguments['sort']);
			}
			$limit = NULL;
			if (isset($arguments['limit'])) {
				$limit = $arguments['limit'];
				unset($arguments['limit']);
			}
			
			// filter arguments
			if (!is_null($arguments) && is_array($arguments) && count($arguments) > 0) {
				$query .= " WHERE ";
				$args = array();
				foreach ($arguments as $arg => $val) {
					$arg = $self->column_prefix . $arg;
					$args[] = "`$arg` " . (is_numeric($val) ? " = " : " LIKE ") . " " . $self->Escape($val);
				}
				$query .= implode (" AND ",$args);
			}
			
			// order arguments
			if (is_array($order)) {
				$query .= " ORDER BY " . $order['column'] . " " . $order['direction'];
			}
			
			// limit arguments
			if (is_array($limit)) {
				if (isset($limit['start']))
					$query .= " LIMIT " . $limit['start'] . "," . $limit['count'];
				else	
					$query .= " LIMIT " . $limit['count'];
			}
			
			$query .= ";";
			$result = mysql_query($query) or die("ERROR: " . mysql_error());
			if (mysql_num_rows($result) > 0) {
				$rows = array();
				while ($row = mysql_fetch_assoc($result)) {
					$idcol = $self->column_prefix . "id";
					if ($self->column_prefix) {
						$row_noprefix = array();
						foreach ($row as $key => $val) {
							$row_noprefix[str_replace($self->column_prefix,'',$key)] = stripslashes($val);
						}
						$row = $row_noprefix;
					}
					else {
						foreach ($row as $key => $val) {
							$row[$key] = stripslashes($val);
						}
					}
					if (isset($row['id']))
						$rows[$row['id']] = $row;
				}
				return $rows;
			}
			else return NULL;
		}
	}
	class DBUpdaterActions {
		static function Update(rDBUpdater $self, array $arguments, $id = NULL) {
			$table = $self->ParseTable();
			$query = "UPDATE $table SET ";
			
			if (!isset($id) && isset($arguments['id'])) {
				$id = $arguments['id'];
				unset($arguments['id']);
			}
			if (is_null($id))
				return NULL;
			
			$set = array();
			foreach ($arguments as $col => $value) {
				$col = $self->column_prefix . $col;
				if (!isset($self->reflection[$col])) {
					continue;
				}
				$set[] = "`$col`=" . $self->Escape($value);
			}
			$query .= implode(",",$set);
			$idcol = $self->column_prefix . "id";
			$query .= " WHERE `$idcol`=$id;";
			mysql_query($query) or die("ERROR: " . mysql_error());
			return $id;
		}
	}
	class DBDeleterActions {
		static function Delete(rDBDeleter $self, $id) {
			$table = $self->ParseTable();
			$query = "DELETE FROM $table WHERE id=$id;";
			mysql_query($query) or die("ERROR: " . mysql_error());
			return true;
		}
	}
?>
