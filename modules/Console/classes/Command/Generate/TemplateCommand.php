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
        $this->_author = $input->getOption( 'author' ) ? $input->getOption( 'author' ) : $_SERVER[ 'USER' ];

        $msg = sprintf( '<info>Begin generate class by name "%s"</info>', $name );
        $output->writeln( $msg );

        $this->checkModuleExists( $name );

        $this->_classInfo = \Miao\Autoload\ClassInfo::parse( $name );

        $template = $this->_getTemplateName();

        $app = \Miao\App::getInstance();
        $path = $app->getPath();
        $templateDir = $path->getTemplateDir( $name );

        $templateFilename = $templateDir . DIRECTORY_SEPARATOR . $template;

        $classTemplateFolder = $path->getTemplateDir( 'Miao\\Console\\Command\\Generate\\TemplateCommand' );
        $sourceTemplateFilename = '';

        $classTemplateFilename = $classTemplateFolder . DIRECTORY_SEPARATOR . $template;

    }

    protected function _getTemplateName()
    {
        $classInfo = $this->_classInfo;
        $result = null;
        if ( $classInfo->isView() )
        {
            $result = $classInfo->getClass( true );
            $result = str_replace( $classInfo->getDelimiter(), '_', $result ) . '.tpl';
            $result = strtolower( $result );
        }
        else if ( $classInfo->isViewBlock() )
        {
            $result = 'index.tpl';
        }
        return $result;
    }
}