<?php
    interface rPasswordAttrActionHandler {}
    
    class PasswordsDontMatchException extends WaxException {
        function __construct() {
            parent::__construct("Passwords Don't Match", "The passwords you entered don't match.");
        }
    }
    class EmptyPasswordException extends WaxException {
        function __construct() {
            parent::__construct("Password cannot be blank","The password you supplied was blank");
        }
    }
    class rPasswordAttrActionHandlerActions {
        static function editor(rPasswordAttrActionHandler $self) {
            $ops = $self->GetOptions();
            $default_func = "md5";
            
            $hashfuncs = hash_algos();
            $displayopts = array();
            foreach ($hashfuncs as $indx => $hf) {
                if ($hf == $default_func) {
                    $tmp = $hashfuncs[$indx];
                    $hashfuncs[$indx] = $hashfuncs[0];
                    $hashfuncs[0] = $tmp;
                    break;
                }
            }
            foreach ($hashfuncs as $func) {
                if (strpos($func,',') !== false)
                    continue;
                $displayopts[] = $func;
            }
            
            return array(
                'salt' => substr(md5(time()), 0, 8),
                'hashfuncs' => $displayopts
            );
        }
        
        /**
        * This function is called when POST data is being saved
        * for this attribute type.  Useful things this function can
        * do are:
        * 
        *   - Input validation
        *   - Input sanitization
        *   - Hashing / Custom Function calls
        */
        static function save(rPasswordAttrActionHandler $self, $record) {
            $password = $record[$self->GetName()];
            $password_confirm = $record[$self->GetName() . '_confirm'];
            if ($password == $password_confirm) {
                // hash the password
                $options = $self->GetOptions();
                
                $record[$self->GetName()] = $self->Hash();
                unset($record[$self->GetName() . "_confirm"]);
                return $record;
            }
            else if (empty($record[$self->GetName()])) {
                throw new EmptyPasswordException();
            }
            else {
                throw new PasswordsDontMatchException();
            }
            return false;
        }
    }
?>