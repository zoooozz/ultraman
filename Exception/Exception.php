<?php

namespace ultraman\Exception;

class Exception extends BaseException
{
    protected $code = 500;
    protected $message = 'API Call Error';
}