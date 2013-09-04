<?php
/**
 * @author vpak
 * @date 2013-08-13 16:22:58
 */

namespace Miao\Log\Writer;

class Null extends \Miao\Log\Writer\AbstractWriter
{
    /**
     * Write a message to the log.
     * @param  array $event  event data
     * @return void
     */
    protected function _write( $event )
    {
    }

    /**
     * Create a new instance of \Miao\Log\Writer\Null
     * @param  array|Config $config
     * @return \Miao\Log\Writer\Null
     * @throws \Miao\Log\Exception
     */
    static public function factory( $config )
    {
        return new self();
    }
}