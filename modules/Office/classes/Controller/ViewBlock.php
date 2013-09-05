<?php
/**
 * User: vpak
 * Date: 03.09.13
 * Time: 14:46
 */

namespace Miao\Office\Controller;

class ViewBlock extends \Miao\Office\Controller implements \Miao\Office\Controller\ViewBlockInterface
{
    /**
     * @var \Miao\Template\Native
     */
    protected $_template;

    /**
     * @var string
     */
    protected $_templateFilename;

    /**
     * @var string
     */
    protected $_templateDir;

    /**
     * @var bool
     */
    protected $_debugMode = null;

    /**
     * @var array
     */
    protected $_params;

    /**
     * @param \Miao\Template\Native $template
     */
    public function setTemplate( $template )
    {
        $this->_template = $template;
    }

    /**
     * @return \Miao\Template\Native
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * @param string $templateDir
     */
    public function setTemplateDir( $templateDir )
    {
        $this->_templateDir = $templateDir;
    }

    /**
     * @return string
     */
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

    /**
     * @param $templateFilename
     * @return $this
     */
    public function setTemplateFilename( $templateFilename )
    {
        $this->_templateFilename = $templateFilename;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateFilename()
    {
        if ( !$this->_templateFilename )
        {
            $this->_templateFilename = 'index.tpl';
        }
        return $this->_templateFilename;
    }

    /**
     * @param array $params
     */
    public function setParams( $params )
    {
        $this->_params = $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    public function getParam( $name, $defaultValue = null, $throwException = true )
    {
        assert( !empty( $name ) );
        assert( is_string( $name ) );

        $result = $defaultValue;
        if ( array_key_exists( $name, $this->_params ) )
        {
            $result = $this->_params[ $name ];
        }
        else if ( $throwException )
        {
            $msg = sprintf( 'Param "%s" not found', $name );
            throw new \Miao\Office\Controller\ViewBlock\Exception( $msg );
        }
        return $result;
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
     * (non-PHPdoc)
     * @see \Miao\Office\Controller\ViewBlockInterface::processData()
     */
    public function processData()
    {
        throw new \Miao\Office\Controller\ViewBlock\Exception( spritnf(
            'Redeclare method "%s" in children classes', __METHOD__
        ) );
    }

    /**
     * (non-PHPdoc)
     * @see \Miao\Office\Controller\ViewBlockInterface::initTemplateVariables()
     */
    public function initTemplateVariables()
    {
        throw new \Miao\Office\Controller\ViewBlock\Exception( spritnf(
            'Redeclare method "%s" in children classes', __METHOD__
        ) );
    }

    /**
     * (non-PHPdoc)
     * @see \Miao\Office\Controller::generateContent()
     */
    public function generateContent()
    {
        $this->_template = new \Miao\Office\Controller\View\Template(  $this->getTemplateDir(), $this->debugMode(
        ) );
        $this->processData();
        $this->initTemplateVariables();
        $result = $this->_template->fetch( $this->getTemplateFilename() );
        return $result;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setTmplVar( $name, $value )
    {
        $this->_template->setValueOf( $name, $value );
        return $this;
    }

    public function __destruct()
    {
        unset( $this->_template );
    }
}