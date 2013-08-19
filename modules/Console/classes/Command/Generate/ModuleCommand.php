<?php

namespace Miao\Console\Command\Generate;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ModuleCommand extends \Symfony\Component\Console\Command\Command
{
    protected function configure()
    {
        $this
            ->setName( 'miao:generate-module' )
            ->setDescription( 'Create module' )
            ->addArgument(
                'name', InputArgument::REQUIRED,
                'Module name, must content two parts. Example: \\\\Miao\\\\NewModule, Miao_NewModule'
            );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $name = $input->getArgument( 'name' );

        $msg = sprintf( '<info>Generate module by name "%s"</info>', $name );
        $output->writeln( $msg );

        $classInfo = \Miao\Autoload\ClassInfo::parse( $name );
        $libName = $classInfo->getLib();
        $moduleName = $classInfo->getModule();

        if ( $moduleName )
        {
            $msg = sprintf( '<info>Library name is "%s", module name is "%s"</info>', $libName, $moduleName );
            $output->writeln( $msg );
        }
        else
        {
            $msg = sprintf(
                '<error>Invalid module name, must content two parts. Example: \\\\Miao\\\\NewModule, Miao_NewModule.</error>'
            );
            $output->writeln( $msg );
        }

        $app = \Miao\App::getInstance();
        $path = $app->getPath();
        $dir = $path->getModuleDir( $name );

        $msg = sprintf( '<info>Directory is "%s"</info>', $dir );
        $output->writeln( $msg );

        $isDirEmpty = \Miao\Path\Helper::isDirEmpty( $dir, false );
        if ( !file_exists( $dir ) && ( true === $isDirEmpty || is_null( $isDirEmpty ) ) )
        {
            $folders = array( 'build', 'classes', 'tests', 'templates', 'tests/classes', 'tests/sources' );
            mkdir( $dir );
            $msg = sprintf( '<info>...Created dir (%s</info>)', $dir );
            $output->writeln( $msg );

            foreach ( $folders as $folder )
            {
                $filename = $dir . DIRECTORY_SEPARATOR . $folder;
                mkdir( $filename );
                $msg = sprintf( '<info>...Created dir (%s)</info>', $filename );
                $output->writeln( $msg );
            }
        }
        else
        {
            $msg = sprintf( '<error>Directory (%s) exists and not empty</error>', $dir );
            $output->writeln( $msg );
        }
    }
}