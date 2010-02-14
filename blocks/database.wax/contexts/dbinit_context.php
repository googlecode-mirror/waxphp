<?php
    class DatabaseInitCtx extends Context {
        private $dsn;
        private $username;
        private $password;
                
        function __construct($dsn,$username = NULL,$password = NULL) {
            $this->dsn = $dsn;
            $this->username = $username;
            $this->password = $password;
        }
        function Execute() {
            $pdo = new WaxPDO($this->dsn, $this->username, $this->password);
            DataSourceManager::Register($this->dsn,$pdo);
            return $this->dsn;
        }
    }
?>