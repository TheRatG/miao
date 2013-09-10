<?php
/**
 * User: vpak
 * Date: 03.09.13
 * Time: 14:45
 */

namespace Miao\Office;

abstract class Controller
{
    /**
     * @var \Miao\Office\Response
     */
    protected $_response = null;

    /**
     * @param Response $response
     */
    public function setResponse( \Miao\Office\Response $response )
    {
        $this->_response = $response;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        if ( is_null( $this->_response ) )
        {
            $this->_response = \Miao\App::getInstance()
                ->getObject( \Miao\App::INSTANCE_RESPONSE_NICK, false );
            if ( !$this->_response )
            {
                $this->_response = new \Miao\Office\Response();
            }
        }
        return $this->_response;
    }

    /**
     * Generate html content
     * @return string
     */
    abstract public function generateContent();

    protected function _init()
    {
    }
}