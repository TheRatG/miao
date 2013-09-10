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
            ->setAliases( array( 'miao:gm' ) )
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
            $this->_createDir( $dir, array( '/' ), $output );
            $folders = array( 'build', 'classes', 'tests', 'tests/classes', 'tests/sources' );
            $this->_createDir( $dir, $folders, $output, 3 );

            $isOffice = 0 === strpos( strrev( $classInfo->getModule() ), strrev( 'Office' ) );
            if ( $isOffice )
            {
                $folders = array(
                    'templates',
                    'templates/layouts',
                    'templates/shared',
                    'templates/View',
                    'templates/ViewBlock'
                );
                $this->_createDir( $dir, $folders, $output, 6 );

                $classTemplateFolder = $path->getTemplateDir( __CLASS__ );
                copy( $classTemplateFolder . '/layouts/index.tpl', $dir . '/templates/layouts/index.tpl' );
                $msg = sprintf(
                    '<info>%sCreated file (%s)</info>', str_repeat( '.', 6 ), $dir . '/templates/layouts/index.tpl'
                );
                $output->writeln( $msg );
            }
        }
        else
        {
            $msg = sprintf( '<error>Directory (%s) exists and not empty</error>', $dir );
            $output->writeln( $msg );
        }
    }

    protected function _createDir( $dir, array $folders, OutputInterface $output, $level = 0 )
    {
        foreach ( $folders as $folder )
        {
            $filename = $dir . DIRECTORY_SEPARATOR . $folder;
            mkdir( $filename );
            $msg = sprintf( '<info>%sCreated dir (%s)</info>', str_repeat( '.', $level ), $filename );
            $output->writeln( $msg );
        }
    }
}