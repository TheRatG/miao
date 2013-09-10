<?php
/**
 * User: vpak
 * Date: 03.09.13
 * Time: 14:46
 */

namespace Miao\Office\Controller;

class View extends \Miao\Office\Controller implements \Miao\Office\Controller\ViewInterface
{
    /**
     * @var string
     */
    protected $_layout;

    /**
     * @var string
     */
    protected $_layoutTemplateDir;

    /**
     * @var string
     */
    protected $_templateFilename;

    /**
     * @var string
     */
    protected $_templateDir;

    /**
     * @var array
     */
    protected $_templateVariables = array();

    /**
     * @var bool
     */
    protected $_debugMode = false;

    /**
     * @var \Miao\Office\Controller\View\Template
     */
    protected $_template;

    /**
     * @param string $layout
     */
    public function setLayout( $layout )
    {
        $this->_layout = $layout;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        if ( !$this->_layout )
        {
            $this->_layout = 'index.tpl';
        }
        return $this->_layout;
    }

    /**
     * @param string $templateFilename
     */
    public function setTemplateFilename( $templateFilename )
    {
        $this->_templateFilename = $templateFilename;
    }

    /**
     * @return string
     */
    public function getTemplateFilename()
    {
        if ( !$this->_templateFilename )
        {
            $path = \Miao\App::getInstance()
                ->getPath();
            $this->_templateFilename = $path->getTemplateNameByClassName( get_called_class() );
        }
        return $this->_templateFilename;
    }

    public function getTemplateDir()
    {
        if ( !$this->_templateDir )
        {
            $path = \Miao\App::getInstance()
                ->getPath();
            $this->_templateDir = $path->getTemplateDir( get_called_class() );
        }
        return $this->_templateDir;
    }

    public function getLayoutTemplateDir()
    {
        if ( !$this->_layoutTemplateDir )
        {
            $this->_layoutTemplateDir = realpath( $this->getTemplateDir() . '/..' ) . '/layouts';
        }
        return $this->_layoutTemplateDir;
    }

    /**
     * @param $state
     * @return bool If it enable, exceptions messages will show in the result of fetch.
     */
    public function debugMode( $state = null )
    {
        if ( !is_null( $state ) )
        {
            $this->_debugMode = (bool)$state;
        }
        return $this->_debugMode;
    }

    /**
     * Init Viewblock
     * @param $name
     * @param ViewBlock $viewBlock
     * @return $this
     * @throws View\Exception
     */
    public function initBlock( $name, \Miao\Office\Controller\ViewBlock $viewBlock )
    {
        $this->_template->initBlock( $name, $viewBlock );
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \Miao\Office\Controller::generateContent()
     */
    public function generateContent()
    {
        $this->_template = new \Miao\Office\Controller\View\Template( $this->getLayoutTemplateDir(), $this->debugMode(
        ) );
        $this->_template->setViewTemplateFilename(
            $this->getTemplateDir() . DIRECTORY_SEPARATOR . $this->getTemplateFilename()
        );
        $this->_template->setValueOfByArray( $this->_templateVariables );
        $this->initializeBlock();
        $result = $this->_template->fetch( $this->getLayout() );
        return $result;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setTmplVar( $name, $value )
    {
        $this->_templateVariables[ $name ] = $value;
        return $this;
    }

    public function initializeBlock()
    {
    }
}