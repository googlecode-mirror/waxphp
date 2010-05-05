<?php
    // the core of IWH-- the datasource the translates into the IWH database format
    // extends WaxPDO
    
    class InvalidTypeException extends WaxException {
        function __construct($type) {
            parent::__construct("Error with Type","`$type` is not a valid data type.");
        }
    }
    
    class DDS extends WaxPDO implements iWaxDataSource {
        private function getModelID($name) {
            $q = "SELECT * FROM models WHERE name LIKE :name;";
            $stmt = $this->prepare($q);
            $stmt->bindValue("name",$name);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $errchk = $stmt->errorInfo();
            if ($errchk[0] != '00000') {
                throw new WaxPDOException($errchk);
            }
            
            return $row['id'];
        }
        function prepare($query) {
            // can be used to intercept incoming queries and print them for debugging
            return parent::prepare($query);
        }
        // creates a model with the given structure
        function CreateType($name, $attributes = array(), $attr_default = NULL) {
            // insert a record into models
            $this->beginTransaction();
            
            $q = "INSERT INTO models (`name`) VALUES (:name);";
            $stmt = $this->prepare($q);
            $stmt->bindValue("name",$name);
            $stmt->execute();
            
            $errchk = $stmt->errorInfo();
            if ($errchk[0] != '00000') {
                $this->rollBack();
                throw new WaxPDOException($errchk);
            }
            
            $model_type_id = $this->lastInsertId();
            
            // insert the attributes into model_structure
            foreach ($attributes as $name => $type) {
                $q = "INSERT INTO model_structure (`name`,`type`,`model_id`) VALUES (:name, :type, :model_id);";
                $stmt = $this->prepare($q);
                $stmt->bindValue("name",$name);
                $stmt->bindValue("type",$type);
                $stmt->bindValue("model_id",$model_type_id);
                
                $stmt->execute();
                $errchk = $stmt->errorInfo();
                if ($errchk[0] != '00000') {
                    $this->rollBack();
                    throw new WaxPDOException($errchk);
                }
            }
            
            $this->commit();
        }
        function ExamineType($name, $detailed = false) {
            $q = "SELECT *,model_structure.id as structure_id FROM models " .
                "JOIN model_structure ON model_structure.model_id = models.id " . 
                "WHERE models.name LIKE :name;";
            $stmt = $this->prepare($q);
            $stmt->bindValue("name",$name);
            
            $structure = array();
            $stmt->execute();
            
            $errchk = $stmt->errorInfo();
            if ($errchk[0] != '00000') {
                throw new WaxPDOException($errchk);
            }
            
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $structure[$row['name']] = $row['type'];
                
                if ($detailed) {
                    $oq = "SELECT * FROM structure_options WHERE structure_id = :struct_id";
                    $ostmt = $this->prepare($oq);
                    $ostmt->bindValue("struct_id",$row['structure_id']);
                    $ostmt->execute();
                    
                    $structure[$row['name']] = array(
                        "id" => $row['structure_id'],
                        "name" => $row['name'],
                        "type" => $row['type'],
                        "default" => $row['default'],
                        "label" => $row['label'],
                        "order" => $row['order'],
                        "options" => array()
                    );
                    
                    foreach ($ostmt->fetchAll(PDO::FETCH_ASSOC) as $option) {
                        $structure[$row['name']]['options'][$option['name']] = $option['value'];
                    }
                }
            }
            return $structure;
        }
        function AlterAttribute($struct, $attr_data, $option_data = array()) {
            $this->beginTransaction();
            
            $q = "UPDATE model_structure SET `name`=:name, `type`=:type, `default`=:default, `label`=:label WHERE `id` = :id";
            $stmt = $this->prepare($q);
            $errchk = $this->errorInfo();
            if ($errchk[0] != '00000') {
                $this->rollBack();
                throw new WaxPDOException($errchk);
            }
            
            $stmt->bindValue("id",$struct);
            foreach ($attr_data as $col => $val) {
                $stmt->bindValue($col,$val);
            }
            if (!isset($attr_data['default']))
                $stmt->bindValue("default","");
            if (!isset($attr_data['label'])) 
                $stmt->bindValue("label","");
            $stmt->execute();
            
            $errchk = $stmt->errorInfo();
            if ($errchk[0] != '00000') {
                $this->rollBack();
                throw new WaxPDOException($errchk);
            }
            
            $q = "SELECT * FROM structure_options WHERE structure_id = :id;";
            $stmt = $this->prepare($q);
            $stmt->bindValue("id",$struct);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $optionlist = array();

            foreach ($results as $option) {
                if (!isset($option_data[$option['name']])) {
                    $dq = "DELETE FROM structure_options WHERE id=:id";
                    $dstmt = $this->prepare($dq);
                    $dstmt->bindValue($option['id']);
                    $dstmt->execute();
                    
                    $errchk = $stmt->errorInfo();
                    if ($errchk[0] != '00000') {
                        $this->rollBack();
                        throw new WaxPDOException($errchk);
                    }
                }
                else {
                    $optionlist[$option['name']] = $option['value'];
                }
            }
            
            $iq = "INSERT INTO structure_options (`structure_id`,`name`,`value`) VALUES (:struct_id, :name, :value);";
            $istmt = $this->prepare($iq);
            foreach ($option_data as $name => $value) {
                if (!isset($optionlist[$name]) && !empty($name) && !empty($value)) {
                    $istmt->execute(array(
                        "struct_id" => $struct,
                        "name" => $name,
                        "value" => $value
                    ));
                    
                    $errchk = $istmt->errorInfo();
                    if ($errchk[0] != '00000') {
                        $this->rollBack();
                        throw new WaxPDOException($errchk);
                    }
                }
            }
            $this->commit();
        }
        function AlterType($name, $attr_add = NULL, $attr_remove = NULL, $attr_rename = NULL) {
            // remove attrs first
            $this->beginTransaction();
            $model_id = $this->getModelID($name);
            
            if (is_null($model_id) || $model_id == 0) {
                throw new InvalidTypeException($name);
            }
            
            if (is_array($attr_remove)) {
                $q = "DELETE FROM model_structure WHERE model_id = :model_id AND name LIKE :attr;";
                $stmt = $this->prepare($q);
                foreach ($attr_remove as $attr) {
                    $stmt->execute(array(
                        "model_id" => $model_id, 
                        "attr" => $attr
                    ));
                    $errchk = $stmt->errorInfo();
                    if ($errchk[0] != '00000') {
                        $this->rollBack();
                        throw new WaxPDOException($errchk);
                    }
                }
            }
            
            // add the new ones
            if (is_array($attr_add)) {
                $q = "INSERT INTO model_structure (`name`,`type`,`model_id`) VALUES (:name, :type, :model_id);";
                $stmt = $this->prepare($q);
                foreach ($attr_add as $attr => $type) {
                    $stmt->execute(array(
                        "name" => $attr,
                        "type" => $type,
                        "model_id" => $model_id
                    ));
                    $errchk = $stmt->errorInfo();
                    if ($errchk[0] != '00000') {
                        $this->rollBack();
                        throw new WaxPDOException($errchk);
                    }
                }
            }
            
            // perform renames
            if (is_array($attr_rename)) {
                $q = "UPDATE model_structure SET `name`=:name WHERE `name` LIKE :name_from AND model_id = :model_id";
                $stmt = $this->prepare($q);
                foreach ($attr_rename as $attr_from => $attr_to) {
                    $stmt->execute(array(
                        "name" => $attr_to,
                        "name_from" => $attr_from,
                        "model_id" => $model_id
                    ));
                    $errchk = $stmt->errorInfo();
                    if ($errchk[0] != '00000') {
                        $this->rollBack();
                        throw new WaxPDOException($errchk);
                    }
                }
            }
            $this->commit();
        }
        function DeleteType($name) {
            $q = "DELETE FROM models WHERE name LIKE :name;";
            $stmt = $this->prepare($q);
            $stmt->bindValue("name",$name);
            $stmt->execute();
            $errchk = $stmt->errorInfo();
            if ($errchk[0] != '00000') {
                throw new WaxPDOException($errchk);
            }
        }
        function ListTypes() {
            $q = "SELECT * FROM models;";
            $stmt = $this->prepare($q);
            $stmt->execute();
            $errchk = $stmt->errorInfo();
            if ($errchk[0] != '00000') {
                throw new WaxPDOException($errchk);
            }
            
            $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $ret = array();
            foreach ($types as $attr) {
                $ret[$attr['id']] = $attr['name'];
            }
            return $ret;
        }
        function Find($type, $filters = array()) {
            /***
                SELECT * FROM record_data
                WHERE record_data.record_id IN (
                	SELECT DISTINCT record_data.record_id FROM record_data
                	JOIN model_structure ON model_structure.id = record_data.structure_id
                	WHERE (model_structure.name = 'Description' AND record_data.data LIKE '%black%')
                );
            ***/
            $q = <<<QUERY
SELECT *,model_structure.name as attr_name FROM record_data
JOIN model_structure ON model_structure.id = record_data.structure_id
JOIN models ON model_structure.model_id = models.id
WHERE models.name LIKE :model_name
QUERY;
                
            // _id is a special case --> find this record
            if (isset($filters['_id'])) {
                $q .= " AND record_data.record_id=:id";
            }
            else {
                $qconds = array();
                foreach ($filters as $column => $value) {
                    $qconds[] = "record_data.record_id IN (\n" . 
                                "SELECT record_data.record_id FROM record_data \n" . 
                                "JOIN model_structure ON model_structure.id = record_data.structure_id \n" . 
                                "WHERE (`model_structure`.`name`=:${column}_name AND `data` LIKE :${column}_value)\n" . 
                                ")";
                    $qargs[$column . "_name"] = $column;
                    $qargs[$column . "_value"] = $value;
                }
                if (count($qconds) > 0) {
                    $q .= " AND ";
                    $q .= implode(" AND ",$qconds);
                }
            }
            $q .= ";";
            
            $stmt = $this->prepare($q);
            $stmt->bindValue("model_name",$type);
            if (isset($filters['_id'])) {
                $stmt->bindValue("id",$filters['_id']);
            }
            else {
                foreach ($filters as $column => $value) {
                    $q = str_replace(
                        array(
                            ":${column}_name",
                            ":${column}_value"
                        ),
                        array(
                            "'" . $column . "'",
                            "'" . $value . "'"
                        ),
                        $q
                    );
                    $stmt->bindValue($column . "_name",$column);
                    $stmt->bindValue($column . "_value",$value);
                }
            }

            $stmt->execute();
            $errchk = $stmt->errorInfo();
            if ($errchk[0] != '00000') {
                throw new WaxPDOException($errchk);
            }
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $ret = array();
            foreach ($records as $record_attr) {
                if (!isset($ret[$record_attr['record_id']]))
                    $ret[$record_attr['record_id']] = array('_id' => $record_attr['record_id']);
                    
                $ret[$record_attr['record_id']][$record_attr['attr_name']] = $record_attr['data'];
            }
            return $ret;    
        }
        function FindByID($type, $id) {
            $result = $this->Find($type,array("_id" => $id));
            if (count($result) > 0) {
                $part = array_values($result);
                return $part[0];
            }
            else return array();
        }
        function Save($type, $data) {
            $this->beginTransaction();
            
            // get the model id
            $q = "SELECT * FROM models WHERE name LIKE :name;";
            $stmt = $this->prepare($q);
            $stmt->bindValue("name",$type);
            $stmt->execute();
            $errchk = $stmt->errorInfo();
            if ($errchk[0] != '00000') {
                throw new WaxPDOException($errchk);
            }
            $model_id = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($stmt->rowCount() == 0) {
                throw new InvalidTypeException("Unknown Type: $type");
            }
            $model_id = $model_id['id'];
            
            
            // get the record id -- create a new record if necessary 
            $record_id = NULL;
            $record = array();
            $structure = $this->ExamineType($type,true);

            if (isset($data['_id'])) {
                $record_id = $data['_id'];
                $record = $this->Find($type, array("_id" => $data['_id']));
                unset($data['_id']);
            }
            else {
                $q = "INSERT INTO records (`model_id`) VALUES (:model_id);";
                $stmt = $this->prepare($q);
                $stmt->bindValue("model_id",$model_id);
                $stmt->execute();
                $errchk = $stmt->errorInfo();
                if ($errchk[0] != '00000') {
                    $this->rollBack();
                    throw new WaxPDOException($errchk);
                }
                $record_id = $this->lastInsertID();
            }
            
            foreach ($data as $attr => $value) {
                $attr_id = 0;
                
                if (!is_numeric($attr))
                    $attr = $structure[$attr]['id'];

                $q = "UPDATE record_data SET data = :data WHERE structure_id = :structure_id AND record_id = :record_id";
                if (!isset($record[$attr]))
                    $q = "INSERT INTO record_data (`data`,`structure_id`,`record_id`) VALUES (:data, :structure_id, :record_id);";

                $tuple = array(
                    "data" => $value,
                    "structure_id" => $attr,
                    "record_id" => $record_id
                );
                $stmt = $this->prepare($q);
                $stmt->execute($tuple);
            }
            $this->commit();
        }
        function Delete($type, $id) {
            $this->beginTransaction();
            
            $q = "DELETE FROM record_data WHERE record_id = :id;";
            $stmt = $this->prepare($q);
            $stmt->bindValue('id', $id);
            $stmt->execute();
            
            $errchk = $stmt->errorInfo();
            if ($errchk[0] != '00000') {
                $this->rollBack();
                throw new WaxPDOException($errchk);
            }
            
            $q = "DELETE FROM records WHERE id = :id;";
            $stmt = $this->prepare($q);
            $stmt->bindValue(":id",$id);
            $stmt->execute();
            
            $errchk = $stmt->errorInfo();
            if ($errchk[0] != '00000') {
                $this->rollBack();
                throw new WaxPDOException($errchk);
            }
            
            $this->commit();
        }
        
        function ACL_Get($recordid = NULL, $resourceid = NULL) {
            $stmt = NULL;
            if (is_null($recordid) && is_null($resourceid)) {
                wax_error("Cannot have record id and resource id be null in an ACL lookup");
                return;
            }
            else if (is_null($recordid)) {
                $q = "SELECT * FROM `acl` WHERE `resource` LIKE :resource;";
                $stmt = $this->prepare($q);
                $stmt->bindValue("resource",$resourceid);
            }
            else if (is_null($resourceid)) {
                $q = "SELECT * FROM `acl` WHERE `for`=:record_id;";
                $stmt = $this->prepare($q);
                $stmt->bindValue("record_id",$recordid);
            }
            else {
                $q = "SELECT * FROM `acl` WHERE `for`=:record_id and `resource` LIKE :resource;";
                $stmt = $this->prepare($q);
                $stmt->bindValue("record_id",$recordid);
                $stmt->bindValue("resource",$resourceid . "%");
            }
            $stmt->execute();
            
            $errchk = $stmt->errorInfo();
            if ($errchk[0] != '00000') {
                $this->rollBack();
                throw new WaxPDOException($errchk);
            }
            
            $result = $stmt->fetchAll();
            $ret = array();
            foreach ($result as $permission) {
                if ($permission['permission'] == "DENY")
                    $ret[$permission['resource']] = false;
                else
                    $ret[$permission['resource']] = true;
            }
            
            return $ret;
        }
        
        // give or remove access to $resourceid as object $recordid
        function ACL_Set($recordid, $resourceid, $allow_deny = "ALLOW") {
            $this->beginTransaction();
            
            // get the current permissions
            $permissions = $this->ACL_get($recordid);
            
            if ($allow_deny === NULL) {
                if (isset($permissions[$resourceid])) {
                    $q = "DELETE FROM `acl` WHERE `for`=:record_id AND `resource`=:resource_id;";
                    $stmt = $this->prepare($q);
                    $stmt->bindValue("record_id",$recordid);
                    $stmt->bindValue("resource_id",$resourceid);
                    _debug("Removing $recordid's permissions for $resourceid");
                }
                else return;
            }
            else {
                $q = "INSERT INTO `acl` (`for`,`resource`,`permission`) VALUES (:record_id, :resource_id, :permission);";
                if (isset($permissions[$resourceid]))
                    $q = "UPDATE `acl` SET `permission`=:permission WHERE `for`=:record_id AND `resource`=:resource_id;";
            
                $stmt = $this->prepare($q);
                $stmt->bindValue("record_id", $recordid);
                $stmt->bindValue("resource_id", $resourceid);
                $stmt->bindValue("permission", ($allow_deny == "DENY" ? "" : "DENY"));
            }
            $stmt->execute();
            $errchk = $stmt->errorInfo();
            if ($errchk[0] != '00000') {
                $this->rollBack();
                throw new WaxPDOException($errchk);
            }
            
            $this->commit();
        }
    }
?>