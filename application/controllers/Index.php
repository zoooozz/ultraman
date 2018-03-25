<?php 


/**
 *  入口文件信息
 */

use dao\Ecode;
use ultraman\Common\DataValidator;

class IndexController extends ultraman\Yaf\BaseController
{
    public function indexAction()
    {
        throw new \Exception("API Call Error",Ecode::ERROR);
    }

   

}