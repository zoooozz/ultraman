<?php

namespace ultraman\Exception;

class Exception extends \Exception
{
    public function __toString()
    {
        $data['msg'] =  $this->message;
        $data['code'] = $this->code;
        header('Content-Type:application/json; charset=utf-8');
        return json_encode($data);
    }
}
