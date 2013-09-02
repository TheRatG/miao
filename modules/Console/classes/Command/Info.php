<?php
namespace Miao\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Info extends \Symfony\Component\Console\Command\Command
{
    protected function configure()
    {
        $this
            ->setName( 'miao:info' )
            ->setDescription( 'Greet someone' )
            ->addArgument(
                'name', InputArgument::OPTIONAL, 'Who do you want to greet?'
            )
            ->addOption(
                'yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters'
            );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $name = $input->getArgument( 'name' );
        if ( $name )
        {
            $text = 'Hello ' . $name;
        }
        else
        {
            $text = 'Hello';
        }

        if ( $input->getOption( 'yell' ) )
        {
            $text = strtoupper( $text );
        }

        $output->writeln( $text );
    }
}