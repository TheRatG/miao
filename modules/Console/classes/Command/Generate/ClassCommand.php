<?php

namespace Miao\Console\Command\Generate;

use Miao\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ClassCommand extends Command\Generate
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
            ->setName( 'miao:generate-class' )
            ->setAliases( array( 'miao:gc' ) )
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
        $this->_author = $input->getOption( 'author' ) ? $input->getOption( 'author' ) : $_SERVER[ 'USER' ];

        $msg = sprintf( '<info>Begin generate class by name "%s"</info>', $name );
        $output->writeln( $msg );

        $app = \Miao\App::getInstance();
        $path = $app->getPath();
        $dir = $path->getModuleDir( $name );

        $this->_classInfo = \Miao\Autoload\ClassInfo::parse( $name );

        if ( !file_exists( $dir ) )
        {
            $this->_generateModule( $name, $output );
        }

        $template = $this->_getTemplateName();
        $parent = $this->_getParent( $output );

        $error = '';
        try
        {
            $classFilename = $this->_makeFile(
                $this->_classInfo, $template, array( '%author%', '%parent%' ), array( $this->_author, $parent )
            );
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

    protected function _generateModule( $name, OutputInterface $output )
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

    protected function _getTemplateName()
    {
        $result = 'general.tpl';
        if ( $this->_classInfo->isView() )
        {
            $result = 'view.tpl';
            $viewClassName = $this->_classInfo->getLib() . '\\' . $this->_classInfo->getModule() . '\\' . 'View';
            if ( $this->_classInfo->getClass() == $viewClassName )
            {
                $result = 'view_main.tpl';
            }
        }
        else if ( $this->_classInfo->isViewBlock() )
        {
            $result = 'view_block.tpl';
            $viewBlockClassName = $this->_classInfo->getLib() . '\\' . $this->_classInfo->getModule(
                ) . '\\' . 'ViewBlock';
            if ( $this->_classInfo->getClass() == $viewBlockClassName )
            {
                $result = 'view_block_main.tpl';
            }
        }
        else if ( $this->_classInfo->isAction() )
        {
            $result = 'action.tpl';
            $viewBlockClassName = $this->_classInfo->getLib() . '\\' . $this->_classInfo->getModule() . '\\' . 'Action';
            if ( $this->_classInfo->getClass() == $viewBlockClassName )
            {
                $result = 'action_main.tpl';
            }
        }
        return $result;
    }

    protected function _getParent( OutputInterface $output )
    {
        $result = '';
        $parentClassName = '';
        if ( $this->_classInfo->isView() )
        {
            $parentClassName = $this->_classInfo->getLib() . '\\' . $this->_classInfo->getModule() . '\\' . 'View';
            $parentTemplate = 'view_main.tpl';
            $baseClassName = '\\Miao\\Office\\Controller\\View';
        }
        else if ( $this->_classInfo->isViewBlock() )
        {
            $parentClassName = $this->_classInfo->getLib() . '\\' . $this->_classInfo->getModule() . '\\' . 'ViewBlock';
            $parentTemplate = 'view_block_main.tpl';
            $baseClassName = '\\Miao\\Office\\Controller\\ViewBlock';
        }
        else if ( $this->_classInfo->isAction() )
        {
            $parentClassName = $this->_classInfo->getLib() . '\\' . $this->_classInfo->getModule() . '\\' . 'Action';
            $parentTemplate = 'action_main.tpl';
            $baseClassName = '\\Miao\\Office\\Controller\\Action';
        }

        if ( $parentClassName )
        {
            $isParentExists = \Miao\Autoload::getInstance()
                ->getFilenameByClassName( $parentClassName );
            if ( !$isParentExists )
            {
                $parentFilename = $this->_makeFile(
                    \Miao\Autoload\ClassInfo::parse( $parentClassName ), $parentTemplate,
                    array( '%author%', '%parent%' ), array( $this->_author, $baseClassName )
                );

                $msg = sprintf(
                    '<info>...Generated parent class "%s", file "%s"</info>', $baseClassName, $parentFilename
                );
                $output->writeln( $msg );
            }
            $result = '\\' . $parentClassName;
        }
        else
        {
            $msg = sprintf( 'Parent class for "%s" not found', $this->_classInfo->getParsedString() );
            throw new Command\Exception( $msg );
        }
        return $result;
    }
}