<?php
    interface rLoginIdentifier {
    }
    
    class InvalidCredentialsException extends WaxException {
        function __construct($uname) {
            parent::__construct("Invalid Username/Password","Your username and/or password was incorrect.");
        }
    }
    class rLoginIdentifierActions {
        static function Authenticate(rLoginIdentifier $self, $password) {
            $ddm = DSM::Get();  // get the data source
            
            $objstruct = $ddm->ExamineType(get_class($self),true);
            $objstruct['Password']['value'] = $password;
            $pw = new PasswordAttribute($objstruct['Password']);
                        
            $user = $ddm->Find(get_class($self),array(
                        "Username" => $self->GetUsername(),                 // look for this username
                        "Password" => $pw->Hash()   // hash the password
                    ));
                    

            if ($user)
                return $user;
            else
                throw new InvalidCredentialsException($self->GetUsername());
        }
        static function SetSession(rLoginIdentifier $self, $var, $value) {
            @session_start();
            
            $_SESSION[$var] = $value;
            return;
        }
        static function DestroySession(rLoginIdentifier $self) {
            // unset all vars and generate a new id
            foreach ($_SESSION as $var => $value) {
                unset($_SESSION[$var]);
            }
            session_regenerate_id();
        }
    }
?>