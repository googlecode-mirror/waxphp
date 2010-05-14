<?php
    interface rAntiSpamAttrActionHandler {}
    
    class YoureARobotException extends WaxException {
        function __construct() {
            parent::__construct("Bot Detected","You did not pass the human verification test.  Sorry.");
        }
    }
    
    class rAntiSpamAttrActionHandlerActions {
        static function edit(rAntiSpamAttrActionHandler $self) {
            return array('num' => rand() % 100, 'num2' => rand() % 100);
        }
        static function save(rAntiSpamAttrActionHandler $self, $record) {
            $chks = array($self->GetName(), $self->GetName() . "2", $self->GetName() . "_confirm");
            
            _debug($_POST);
            foreach ($chks as $chk) {
                _debug($chk);
                if (!isset($record[$chk]) || empty($record[$chk]) || !is_numeric($record[$chk]))
                    throw new YoureARobotException();
            }
            
            if ($record[$self->GetName()] + $record[$self->GetName() . "2"] == $record[$self->GetName() . "_confirm"]) {
                foreach ($chks as $chk) {
                    unset($record[$chk]);
                }
            }
            else throw new YoureARobotException();
        }
    }
?>