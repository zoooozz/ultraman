<?php


namespace ultraman\Yaf;

use League\CLImate\CLImate;
class Installer
{
    private $appName = 'ultraman-demo';
    private $climate;
    private $type;
    private $directory;

    public function init()
    {   
        $this->climate = new CLImate();    
        $dir = dirname(__FILE__)."/lock";
        if(is_file($dir)){
            $this->climate->lightRed("项目已经创建过 可以删除install 中的lock 重新创建项目\n");
            $this->climate->usage();
            return;
        }
        $this->showWelcome();
        $this->wizard();
    }

    private function wizard()
    {
        $this->appName = $this->getAppNameFromInput();
        $this->install();
    }

    private function showWelcome()
    {
        $this->climate->lightRed("ultraman  奥特曼");        
        $this->climate->lightGreen('Create a new ultraman application.');
    }

    private function getAppNameFromInput()
    {
        $msg = 'Your service name: (ex: ' . $this->appName . ')';
        $input = $this->climate->lightGreen()->input($msg);
        $input->defaultTo($this->appName);
        $response = trim($input->prompt());
        if ($response === $this->appName) {
            $this->climate->blue('Use default application name: ' . $this->appName);
        }
        return $response;
    }

    private function install()
    {
        $this->climate->lightGreen('项目创建成功');
    }


}
