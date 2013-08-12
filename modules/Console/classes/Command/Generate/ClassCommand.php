<?php

namespace Miao\Console\Command\Generate;

use Miao\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ClassCommand extends Command\Generate
{
    protected function configure()
    {
        $this
            ->setName( 'miao:generate-class' )
            ->setDescription( 'Create class' )
            ->addArgument(
                'name', InputArgument::REQUIRED,
                'Module name, must content two parts. Example: \\\\Miao\\\\NewModule, Miao_NewModule'
            )
            ->addOption( 'author', 'a', null, 'Author tag' );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $name = $input->getArgument( 'name' );

        $msg = sprintf( '<info>Begin generate class by name "%s"</info>', $name );
        $output->writeln( $msg );

        $app = \Miao\Application::getInstance();
        $path = $app->getPath();
        $dir = $path->getModuleDir( $name );

        $classInfo = \Miao\Autoload\ClassInfo::parse( $name );

        if ( !file_exists( $dir ) )
        {
            $command = $this
                ->getApplication()
                ->find( 'miao:generate-module' );
            $arguments = array(
                'command' => 'miao:generate-module',
                'name' => $name
            );

            $inputModule = new \Symfony\Component\Console\Input\ArrayInput( $arguments );
            $command->run( $inputModule, $output );
        }

        $author = $input->getOption( 'author' ) ? $input->getOption( 'author' ) : $_SERVER[ 'USER' ];

        $template = 'general.tpl';
        if ( $classInfo->isView() )
        {
            $template = 'view.tpl';
            //@todo: create folder and tpl
        }
        else if ( $classInfo->isViewBlock() )
        {
            $template = 'view_block.tpl';
            //@todo: create folder and tpl
        }

        $error = '';
        try
        {
            $classFilename = $this->_makeFile( $classInfo, $template, $author );
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