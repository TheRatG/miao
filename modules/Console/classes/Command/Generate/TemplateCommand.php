<?php
/**
 * @author vpak
 * @date 2013-09-06 15:58:17
 */

namespace Miao\Console\Command\Generate;

use Miao\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TemplateCommand extends Command\Generate
{
    /**
     * @var \Miao\Autoload\ClassInfo
     */
    protected $_classInfo;

    /**
     * @var string
     */
    protected $_author;

    protected function configure()
    {
        $this
            ->setName( 'miao:generate-template' )
            ->setAliases( array( 'miao:tpl' ) )
            ->setDescription( 'Create template by class name' )
            ->addArgument(
                'name', InputArgument::REQUIRED,
                'Module name, must content two parts. Example: \\\\Miao\\\\NewModule, Miao_NewModule'
            )
            ->addOption( 'author', 'a', null, 'Author tag' );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $name = $input->getArgument( 'name' );
        $author = $input->getOption( 'author' ) ? $input->getOption( 'author' ) : $_SERVER[ 'USER' ];

        $msg = sprintf( '<info>Begin generate class by name "%s"</info>', $name );
        $output->writeln( $msg );

        $this->checkModuleExists( $name );

        $this->_classInfo = \Miao\Autoload\ClassInfo::parse( $name );
        $classInfo = $this->_classInfo;

        $app = \Miao\App::getInstance();
        $path = $app->getPath();

        $classTemplateFolder = $path->getTemplateDir( 'Miao\\Console\\Command\\Generate\\TemplateCommand' );

        $sourceTemplateName = 'index.tpl';
        if ( $classInfo->isView() )
        {
            $sourceTemplateName = 'view.tpl';
        }
        else if ( $classInfo->isViewBlock() )
        {
            $sourceTemplateName = 'view_block.tpl';
        }
        $sourceTemplateFilename = $classTemplateFolder . DIRECTORY_SEPARATOR . $sourceTemplateName;

        $classTemplateFilename = $path->getTemplateDir( $name ) . DIRECTORY_SEPARATOR . $path->getTemplateNameByClassName( $name );

        if ( file_exists( $classTemplateFilename ) )
        {
            $msg = sprintf( 'Template for class "%s" exists by file (%s)', $name, $classTemplateFilename );
            throw new \Miao\Console\Command\Generate\Exception( $msg );
        }

        $string = file_get_contents( $sourceTemplateFilename );
        $search = array(
            '%namespace%',
            '%date%',
            '%author%',
            '%class%'
        );
        $replace = array(
            $classInfo->getNamespace(),
            date( 'Y-m-d H:i:s' ),
            $author,
            $name
        );
        $string = str_replace(
            $search, $replace, $string
        );

        $dir = dirname( $classTemplateFilename );
        if ( !file_exists( $dir ) )
        {
            mkdir( $dir, 0775, true );
        }
        file_put_contents( $classTemplateFilename, $string );

        $msg = sprintf( '<info>Generated file (%s), for class %s</info>', $classTemplateFilename, $name );
        $output->writeln( $msg );

        return $classTemplateFilename;
    }
}