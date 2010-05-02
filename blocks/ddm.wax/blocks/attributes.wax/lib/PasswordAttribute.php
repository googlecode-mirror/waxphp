<?php
    class PasswordAttribute extends Attribute implements rPasswordAttrActionHandler, rPasswordHasher {
        function GetPassword() {
            return $this->GetValue();
        }
        function GetSalt() {
            $options = $this->GetOptions();
            return $options['salt'];
        }
        function GetHashFunc() {
            $options = $this->GetOptions();
            return $options['hashfunc'];
        }
    }
?>