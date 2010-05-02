<?php
    interface rPasswordHasher {
        function GetPassword();
        function GetSalt();
        function GetHashFunc();
    }
    
    class InvalidHashFunctionException extends WaxException {
        function __construct($hf) {
            parent::__construct("Unknown Hash Function: $hf", "$hf is not a valid hash function");
        }
    }
    
    class rPasswordHasherActions {
        static function Hash(rPasswordHasher $self) {
            $password = $self->GetPassword();
            $pwsalt = $self->GetSalt();
            
            $hf = $self->GetHashFunc();
            if (in_array($hf, hash_algos())) {
                $result = hash($hf, $pwsalt . $password);
                return $result;
            }
            else throw new InvalidHashFunctionException($hf);
        }
    }
?>