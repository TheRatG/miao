<?php

namespace Miao\Console\Command\Generate;

use Miao\Path\Exception;
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

        if ( file_exists( $classFilename ) )
        {
            $msg = sprintf( '<error>File %s for class %s exists</error>', $classFilename, $name );
            $output->writeln( $msg );
        }
        else
        {
            $classTemplateFolder = $path->getTemplateDir( '\\Miao\\Console\\Generate\\ClassCommand' );

            $author = $input->getOption( 'author' ) ? $input->getOption( 'author' ) : $_SERVER[ 'USER' ];

            $tpl = 'general.tpl';
            if ( $classInfo->isView() )
            {
                $tpl = 'view.tpl';
            }
            else if ( $classInfo->isViewBlock() )
            {
                $tpl = 'view_block.tpl';
            }
            $classTemplateFilename = $classTemplateFolder . DIRECTORY_SEPARATOR . $tpl;

            $string = file_get_contents( $classTemplateFilename );
            $string = str_replace(
                array(
                     '%author%',
                     '%namespace%',
                     '%date%',
                     '%class%'
                ), array(
                        $author,
                        $classInfo->getNamespace(),
                        date( 'Y-m-d H:i:s' ),
                        $name
                   ), $string
            );

            $dir = dirname( $classFilename );
            if ( !file_exists( $dir ) )
            {
                mkdir( $dir, 0644, true );
            }
            file_put_contents( $classFilename, $string );

            $msg = sprintf( 'Generated file (%s), for class %s', $classFilename, $name );
            $output->writeln( $msg );
        }
    }
}