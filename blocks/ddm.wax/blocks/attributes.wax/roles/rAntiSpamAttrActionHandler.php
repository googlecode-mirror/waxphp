<?php
    interface rAntiSpamAttrActionHandler {}
    
    class YouMightBeSpamException extends WaxException {
        function __construct() {
            parent::__construct("Bot Detected","You did not pass the human verification test.  Sorry.");
        }
    }
    
    class rAntiSpamAttrActionHandlerActions {
        static function edit(rAntiSpamAttrActionHandler $self) {
            return array('num' => rand() % 100, 'num2' => rand() % 100);
        }
        static function save(rAntiSpamAttrActionHandler $self, $record) {
            if ($record[$self->GetName()] + $record[$self->GetName() . "2"] == $record[$self->GetName() . "_confirm"]) {
                unset($record[$self->GetName()]);
                unset($record[$self->GetName() . "2"]);
                unset($record[$self->GetName() . "_confirm"]);
                return $record;
            }
            else throw new YouMightBeSpamException();
        }
    }
?>