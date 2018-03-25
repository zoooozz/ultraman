<?php

namespace ultraman\Exception;

class BaseException extends \Exception
{
    public function __construct($message=null,$code) {
        if(is_null($message)) {
            $message = $this->message; 
        }
        if(is_null($code)) {
            $code = $this->code; 
        }
        parent::__construct($message, $code); 
    }
}