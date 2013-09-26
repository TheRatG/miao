<?php
/**
 *
 * @author vpak
 * @date 2013-09-26 12:39:02
 */

namespace Miao\Session\Container;

class Reference
{
    private $reference;

    public function __construct( &$reference )
    {
        $this->reference = &$reference;
    }

    public function &getReference()
    {
        $reference = &$this->reference;
        return $reference;
    }
}