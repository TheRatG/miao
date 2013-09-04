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

    static public function factory( array $params, $defaultPrefix = null, $defaultParams = array( '_view' => 'Main' ) )
    {

    }

    public function sendResponse()
    {
        $content = $this->_controller->generateContent();
        if ( $content )
        {
            $this->_repsonse->setContent( $content );
        }
        $this->_repsonse->send();
    }
}