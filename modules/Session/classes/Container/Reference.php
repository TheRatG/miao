<?php
/**
 * User: vpak
 * Date: 08.10.13
 * Time: 16:17
 */

namespace Miao\Session\Container;

class Reference
{
    private $_reference;

    public function __construct( &$reference )
    {
        $this->_reference = & $reference;
    }

    public function &getReference()
    {
        $reference = & $this->_reference;
        return $reference;
    }
}