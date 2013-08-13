<?php
/**
 * @author vpak
 * @date 2013-08-13 16:44:24
 */

namespace Miao\Log;

interface FactoryInterface
{
    /**
     * Construct a Miao_Log driver
     * @param  array|\Miao\Config $config
     * @return \Miao\Log\FactoryInterface
     */
    static public function factory( $config );
}