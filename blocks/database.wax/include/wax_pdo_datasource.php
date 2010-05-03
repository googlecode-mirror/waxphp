<?php
    class WaxPDOException extends WaxException {
        function __construct($errinfo) {
            parent::__construct("Database Error ($errinfo[1])",$errinfo[2]);
        }
    }
    // iWaxDataSource defines the input/output methods that
    // an object requires to act as a datasource for wax
    class WaxPDO extends PDO implements iWaxDataSource {        
        function __construct($dsn, $username = NULL, $password = NULL) {
            parent::__construct($dsn, $username, $password);
        }
        
        function CreateType($name, $attributes = array(), $attr_default = NULL) {
            $types = array();
            foreach ($attributes as $colname => $type) {
                $types[] = "`$colname` $type";
            }
            $q = "CREATE TABLE `" . $name . "` (" . implode(",",$types) . ");";
            $stmt = $this->prepare($q);
            $stmt->Execute();
            $errchk = $stmt->errorInfo();
            if ($errchk[0] != '00000') {
                throw new WaxPDOException($errchk);
            }
        }
        function ExamineType($name, $detailed = false) {
            $q = "DESCRIBE `$name`;";
            $stmt = $this->prepare($q);
            $attribs = array();
            
            $stmt->execute();
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $column) {
                $attribs[$column['Field']] = $column['Type'];
            }
            return $attribs;
        }
        function AlterType($name, $attr_add = NULL, $attr_remove = NULL, $attr_rename = NULL) {
        }
        function DeleteType($name) {
            $q = "DROP TABLE `$name`;";
            $stmt = $this->prepare($q);
            $stmt->execute();
        }
        
        function Find($type, $filters = array()) {
            $conds = array();
            $q = "SELECT * FROM `$type`";
            if (!is_null($filters)) {
                foreach ($filters as $col => $cond) {
                    $part = "`$type`.`$col`";
                    if (strpos("%",$cond) !== false) $part .= " LIKE ";
                    else $part .= " = ";
                    $part .= ":$col";
                    $conds[] = $part;
                }
                $q .= " WHERE " . implode(" AND ",$conds);
            }
            $stmt = $this->prepare($q);
            
            if (!is_null($filters)) {
                foreach ($filters as $col => $cond) {
                    $stmt->bindValue($col,$cond);
                }
            }
            $stmt->execute();
            $errchk = $stmt->errorInfo();
            if ($errchk[0] != '00000') {
                throw new WaxPDOException($errchk);
            }
            
            $ret = array();
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $item) {
                $ret[$item['id']] = $item;
            }
            return $ret;
        }
        
        function FindByID($type, $id) {
            return $this->Find($type, array("id" => $id));
        }
        
        function Save($type, $data) {
            if (isset($data['id'])) {
                $id = $data['id'];
                
                unset($data['id']);
                $updates = array();
                foreach ($data as $col => $val) {
                    $updates[] = "`$col`=:$col";
                }
                
                $query = "UPDATE `$type` SET " . implode(", ",$updates) . " WHERE `id`=:id;";
                $stmt = $this->prepare($query);
                foreach ($data as $col => $val) {
                    $stmt->bindValue($col,$val);
                }
                $stmt->bindValue("id",$id);
                
                $stmt->execute();
                $errchk = $stmt->errorInfo();
                if ($errchk[0] != '00000') {
                    throw new WaxPDOException($errchk);
                }
            }
            else return $this->Create($type, $data);
        }
        function Create($type, $data = NULL) {
            $cols = array();
            $vals = array();
            
            foreach ($data as $col => $val) {
                $cols[] = "`$col`";
                $vals[] = ":$col";
            }

            $q = "INSERT INTO `$type` (" . implode(",",$cols) . ") VALUES (" . implode(",",$vals) . ");";
            $stmt = $this->prepare($q);
            
            if (!is_null($data)) {
                foreach ($data as $col => $val) {
                    $stmt->bindValue($col,$val);
                }
            }
            $stmt->execute();
            $errchk = $stmt->errorInfo();
            if ($errchk[0] != '00000') {
                throw new WaxPDOException($errchk);
            }
            
            return $this->lastInsertId();
        }
        function Delete($type, $id) {
            $q = "DELETE FROM `$type` WHERE `id`=:id;";
            $stmt = $this->prepare($q);
            $stmt->bindValue("id",$id);
            $stmt->execute();
            $errchk = $stmt->errorInfo();
            if ($errchk[0] != '00000') {
                throw new WaxPDOException($errchk);
            }
        }
        
        // unimplemented methods
        function ListTypes() {}
        function AlterAttribute($struct, $attr_data, $option_data = array()) {}
        function ACL_Get($recordid = NULL, $resourceid = NULL) {}
        function ACL_Set($recordid, $resourceid, $allow_deny = "ALLOW") {}
        
    }
?>