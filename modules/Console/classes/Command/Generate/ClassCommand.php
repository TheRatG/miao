<?php

namespace Miao\Console\Command\Generate;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ClassCommand extends \Symfony\Component\Console\Command\Command
{
    protected function configure()
    {
        $this
            ->setName( 'miao:generate-class' )
            ->setDescription( 'Create class' )
            ->addArgument(
                'name', InputArgument::REQUIRED,
                'Module name, must content two parts. Example: \\\\Miao\\\\NewModule, Miao_NewModule'
            );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $name = $input->getArgument( 'name' );

        $msg = sprintf( '<info>Generate class by name "%s"</info>', $name );
        $output->writeln( $msg );

        $app = \Miao\Application::getInstance();
        $path = $app->getPath();
        $dir = $path->getModuleDir( $name );

        $classInfo = \Miao\Autoload\ClassInfo::parse( $name );
        $libName = $classInfo->getLib();

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

        $plugin = new \Miao\Autoload\Plugin\Standart( $libName, $path->getRootDir( $libName ) );
        $classFilename = $plugin->getFilenameByClassName( $name );
        $classTemplateFolder = $path->getTemplateDir( '\\Miao\\Console\\Generate\\ClassCommand' );

        $classTemplateFilename = $classTemplateFolder . DIRECTORY_SEPARATOR . 'common.tpl';
    }
}