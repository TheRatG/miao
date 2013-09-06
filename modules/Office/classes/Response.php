<?php
/** 
 * User: vpak
 * Date: 03.09.13
 * Time: 17:13 
 */

namespace Miao\Office;


class Response
{
    protected $_header;

    protected $_content;

    /**
     * @param mixed $content
     */
    public function setContent( $content )
    {
        $this->_content = $content;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->_content;
    }

    public function __construct()
    {
        $this->_header = new \Miao\Office\Header();
    }

    public function __toString()
    {

    }
}