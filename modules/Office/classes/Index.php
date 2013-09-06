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

    static public function factory( array $params, $defaultPrefix = null, $defaultParams = array( '_view' => 'Main' ) )
    {
        $factory = new \Miao\Office\Factory( $defaultPrefix );
        $controllerClassName = $factory->getControllerClassName( $params, $defaultParams );
        if ( !$controllerClassName )
        {
            $msg = 'Invalid params, controller key is broken or does not exists';
            throw new \Miao\Office\Exception( $msg );
        }
        $result = new self( $factory, new $controllerClassName() );
        return $result;
    }

    public function __construct( \Miao\Office\Factory $factory, $controllerClassName )
    {
        $this->_factory = $factory;
        $this->_controller = new $controllerClassName( $this );
    }

    public function setController( \Miao\Office\Controller $controller )
    {
        if ( $controller instanceof \Miao\Office\Controller\Action && !$controller instanceof \Miao\Office\Controller\ActionInterface )
        {
            $msg = spritnf( 'Invalid controller "%s", must be implemented in \Miao\Office\Controller\ActionInterface', get_class( $controller ) );
        }
        else if ( $controller instanceof \Miao\Office\Controller\View && !$controller instanceof \Miao\Office\Controller\ViewInterface )
        {
            $msg = spritnf( 'Invalid controller "%s", must be implemented in \Miao\Office\Controller\ViewInterface', get_class( $controller ) );
        }
        else if ( $controller instanceof \Miao\Office\Controller\ViewBlock && !$controller instanceof \Miao\Office\Controller\ViewBlockInterface )
        {
            $msg = spritnf( 'Invalid controller "%s", must be implemented in \Miao\Office\Controller\ViewBlockInterface', get_class( $controller ) );
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
        return $this->_controller;
    }

    public function getContent()
    {
        $content = $this->_controller->generateContent();
        if ( $content )
        {
            $this->_response->setContent( $content );
        }
        return $content;
    }

    public function sendResponse()
    {
        $this->getContent();
        $this->_response->send();
    }
}