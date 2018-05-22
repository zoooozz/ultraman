<?php 

/**
 * App 基础类
 *
 * @package   ultraman\Foundation
 * @copyright Copyright (c) 2017, ultraman
 * @var 基础服务入口文件
 */


namespace ultraman;

use ultraman\Foundation\DI;
use League\CLImate\CLImate;
use Symfony\Component\Console\Command\Command;

class App
{

    /**
     * 构造函数 入口加载配置文件进行注入
     *
     * @param db    数据库配置
     * @param main  服务配置项
     * @param api   外部接口配置项
     */

    protected $climate;

    /**
     *  入口文件加载
     */

    public function __construct()
    {
        require dirname(dirname(__FILE__)."/../").'/vendor/autoload.php';
    }
    /**
     *  服务器启动
     */

    public function run($params)
    {
        $this->climate = new CLImate();
        $item['service'] = $params[1]??'';
        $item['command'] = $params[2]??'';
        $item['env'] = $params[3]??'';
        DI::set("port", $params[4]??'');
        $this->configure($item['env']);
        if ($item['service'] == '--help') {
            $this->help();
        }
        if ($item['service'] == '-h') {
            if (in_array($item['command'], ['start', 'stop', 'reload'])) {
                call_user_func([$this, $item['command']]);
            } else {
                $this->help();
            }
        }

        if ($item['service'] == '-t') {
            if (in_array($item['command'], ['start', 'stop', 'reload'])) {
                call_user_func([$this, $item['command']]);
            } else {
                $this->help();
            }
        }



        $this->injection();
    }
    
    //启动
    public function start()
    {
        $climate = $this->climate;
        $climate->style->addCommand('rage', ['green','bold']);
        $climate->br(1)->rage('Server is starting....');
        $app = new \ultraman\Http\HttpYafServer();
        $app->run();
    }

    //停止
    private function stop()
    {
        $climate = $this->climate;
        $climate->style->addCommand('rage', ['green','bold']);
        $climate->br(1)->rage('Server is stopping....');
        $app = new \ultraman\Http\HttpYafServer();
        $app->stop();
        $climate->br(1)->rage('Server is stopping....ok');
        die;
    }
    //重载服务
    public function reload()
    {
        $climate = $this->climate;
        $climate->style->addCommand('rage', ['green','bold']);
        $climate->br(1)->rage('Server is reloadping....');
        $app = new \ultraman\Http\HttpYafServer();
        $app->reload();
        $climate->br(1)->rage('Server is reloadping....ok');
        die;
    }

    //帮助
    public function help()
    {
        $climate = $this->climate;
  
        $climate->style->addCommand('rage', ['green','bold']);
        $climate->br(2)->rage('欢迎使用');
        $climate->addArt(dirname(dirname(__FILE__)."/../")."/Foundation/");
        $climate->br(2)->draw('ultraman');
        $climate->style->addCommand('holler', ['underline', 'green', 'bold']);
        $climate->br(2)->holler('帮助');
        $climate->br(2)->info('php cli  (-h|-t)   (start|stop|reload) (env) （port）');
        $climate->br(1)->info('php cli -h start test 启动测试环境');
        $climate->br(1)->info('php cli -h start dev  启动开发环境');
        $climate->br(1)->info('php cli -h start prod 启动正式环境');
        $climate->br(1)->info('php cli -h start pre  启动预发布环境');
        die;
    }

    //环境注入
    private function configure($env='')
    {
        $config = dirname(dirname(dirname(dirname(__FILE__))));
        if ($env != '') {
            $conf = dir($config.'/conf');
            while (false != ($item = $conf->read())) {
                if ($item == '.' || $item == '..') {
                    continue;
                }
                unlink($conf->path.'/'.$item);
            }

            $env = dir($config.'/env/'.$env);
            while (false != ($item = $env->read())) {
                if ($item == '.' || $item == '..') {
                    continue;
                }
                copy($env->path.'/'.$item, $conf->path.'/'.$item);
            }
        }
        $conf = dir($config.'/conf');
        while (false != ($item = $conf->read())) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            $houzhui = substr(strrchr($item, '.'), 1);
            $keys = basename($item, ".".$houzhui);
            $params[$keys] = $conf->path.'/'.$item;
        }
        $this->injection($params);
        return true;
    }

    /**
     *  依赖文件注入
     */

    public function injection($params=[])
    {
        foreach ($params as $key => $class) {
            $f = pathinfo($class, PATHINFO_EXTENSION);
            $conf = "";
            if ($f === 'php') {
                $conf = @include $class;
                if ($conf == "") {
                    continue;
                }
            }
            if ($f === 'ini') {
                $conf = @parse_ini_file($class, true);
                if ($conf == "") {
                    continue;
                }
            }
            DI::set($key, $conf);
        }
        return true;
    }
}
