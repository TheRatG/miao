<?php
/**
 * User: vpak
 * Date: 03.09.13
 * Time: 16:42
 */

namespace Miao\Office;

class Index
{
    /**
     * @var \Miao\Office\Controller
     */
    protected $_controller;

    /**
     * @var \Miao\Office\Response
     */
    protected $_response;

    /**
     * @var \Miao\Office\Factory
     */
    protected $_factory;

    /**
     * @var string
     */
    protected $_controllerClassName;

    /**
     * @var bool
     */
    protected $_debugMode = false;

    static public function factory( array $params, $defaultPrefix = null, $defaultParams = array( '_view' => 'Main' ) )
    {
        $factory = new \Miao\Office\Factory( $defaultPrefix );
        $controllerClassName = $factory->getControllerClassName( $params, $defaultParams );
        if ( !$controllerClassName )
        {
            $msg = 'Invalid params, controller key is broken or does not exists';
            throw new \Miao\Office\Exception( $msg );
        }
        $result = new self( $factory, $controllerClassName );
        return $result;
    }

    public function __construct( \Miao\Office\Factory $factory, $controllerClassName )
    {
        $this->_factory = $factory;
        $this->_controllerClassName = $controllerClassName;
    }

    public function getResponse()
    {
        if  ( !$this->_response )
        {
            $this->_response = new \Miao\Office\Response();
        }
        return $this->_response;
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

    public function setController( \Miao\Office\Controller $controller )
    {
        $msg = '';
        if ( $controller instanceof \Miao\Office\Controller\Action && !$controller instanceof \Miao\Office\Controller\ActionInterface )
        {
            $msg = spritnf(
                'Invalid controller "%s", must be implemented in \Miao\Office\Controller\ActionInterface',
                get_class( $controller )
            );
        }
        else if ( $controller instanceof \Miao\Office\Controller\View && !$controller instanceof \Miao\Office\Controller\ViewInterface )
        {
            $msg = spritnf(
                'Invalid controller "%s", must be implemented in \Miao\Office\Controller\ViewInterface',
                get_class( $controller )
            );
        }
        else if ( $controller instanceof \Miao\Office\Controller\ViewBlock && !$controller instanceof \Miao\Office\Controller\ViewBlockInterface )
        {
            $msg = spritnf(
                'Invalid controller "%s", must be implemented in \Miao\Office\Controller\ViewBlockInterface',
                get_class( $controller )
            );
        }
        if ( $msg )
        {
            throw new \Miao\Office\Exception( $msg );
        }
        $this->_controller = $controller;
    }

    /**
     * @param \Miao\Office\Response $response
     */
    public function setResponse( $response )
    {
        $this->_response = $response;
    }

    /**
     * @return Controller
     */
    public function getController()
    {
        if ( is_null( $this->_controller ) )
        {
            $this->_controller = new $this->_controllerClassName( $this );
            $this->_controller->setResponse( $this->getResponse() );
            if ( method_exists ( $this->_controller, 'debugMode' ) )
            {
                $this->_controller->debugMode( $this->_debugMode );
            }
        }
        return $this->_controller;
    }

    public function getContent()
    {
        $content = $this->getController()->generateContent();
        if ( $content )
        {
            $this->_response->setContent( $content );
        }
        return $content;
    }

    public function sendResponse()
    {
        if ( !$this->_response->getContent() )
        {
            $content = $this->getContent();
            $this->_response->setContent( $content );
        }
        $this->_response->send();
    }
}