<?php
    class DatabaseInitCtx extends Context {
        function Execute($dsn,$username = NULL,$password = NULL) {
            $pdo = new WaxPDO($dsn, $username, $password);
            DataSourceManager::Register($dsn,$pdo);
            return $dsn;
        }
    }
?>