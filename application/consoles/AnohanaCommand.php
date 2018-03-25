<?php

/**
 *  test
 */

namespace consoles;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use ultraman\Log\monoLog;

use League\CLImate\CLImate;

class AnohanaCommand extends Command
{

    protected function configure()
    {
        $this->setName('anohana:init')
        	   ->setDescription('test Anohana')
        	   ->addOption('a',null,InputOption::VALUE_OPTIONAL,'','')
        	   ->addOption('b',null,InputOption::VALUE_OPTIONAL,'','');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {	
       
    } 




}
