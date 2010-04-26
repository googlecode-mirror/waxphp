<?php
    interface rLoginIdentifier {
    }
    class rLoginIdentifierActions {
        static function Authenticate(rLoginIdentifier $self, $password, $hash_func = NULL, $pwd_salt = NULL) {
            $ddm = DSM::Get();  // get the data source
            
            // WARNING: PHP 5.3 (anonymous functions)
            if ($hash_func == NULL) {
                $hash_func = function ($password, $salt) {
                    return md5($password . $salt);
                };
            }
            $user = $ddm->Find(get_class($self),array(
                        "username" => $self->GetUsername(),                 // look for this username
                        "password" => $hash_method($password, $pwd_salt))   // hash the password
                    );
                    
            if ($user)
                return $user['_id'];
            else
                return false;
        }
        static function SetSession($var, $value) {
            @session_start();
            
            $_SESSION[$var] = $value;
            return;
        }
        static function DestroySession() {
            // unset all vars and generate a new id
            foreach ($_SESSION as $var => $value) {
                unset($_SESSION[$var]);
            }
            session_regenerate_id();
        }
    }
?>