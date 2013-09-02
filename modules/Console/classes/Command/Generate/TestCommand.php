<?php

namespace Miao\Console\Command\Generate;

use Miao\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command\Generate
{
    protected function configure()
    {
        $this
            ->setName( 'miao:generate-test' )
            ->setDescription( 'Create test class' )
            ->addArgument(
                'name', InputArgument::REQUIRED,
                'Module name, must content two parts. Example: \\\\Miao\\\\NewModule, \\\\Miao\\\\NewModuleTest'
            )
            ->addOption( 'author', 'a', null, 'Author tag' );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $name = $input->getArgument( 'name' );
        $author = $input->getOption( 'author' ) ? $input->getOption( 'author' ) : $_SERVER[ 'USER' ];

        if ( 0 !== stripos( strrev( $name ), 'tset' ) )
        {
            $name .= 'Test';
        }

        $msg = sprintf( '<info>Begin generate class by name "%s"</info>', $name );
        $output->writeln( $msg );

        $classInfo = \Miao\Autoload\ClassInfo::parse( $name );

        $error = '';
        try
        {
            $classFilename = $this->_makeFile( $classInfo, 'test.tpl', array( '%author%' ), array( $author ) );
        }
        catch ( Command\Exception $e )
        {
            $error = $e->getMessage();
        }

        if ( $error )
        {
            $output->writeln( '<error>' . $msg . '</error>' );
        }
        else
        {
            $msg = sprintf( '<info>Generated file (%s), for class %s</info>', $classFilename, $name );
            $output->writeln( $msg );
        }
    }
}