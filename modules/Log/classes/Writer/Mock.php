<?php
/**
 * @author vpak
 * @date 2013-08-13 16:23:12
 */

namespace Miao\Log\Writer;

class Mock extends \Miao\Log\Writer\AbstractWriter
{
    /**
     * array of log events
     */
    public $events = array();

    /**
     * shutdown called?
     */
    public $shutdown = false;

    /**
     * Write a message to the log.
     * @param  array $event  event data
     * @return void
     */
    public function _write( $event )
    {
        $this->events[ ] = $event;
    }

    /**
     * Record shutdown
     * @return void
     */
    public function shutdown()
    {
        $this->shutdown = true;
    }

    /**
     * Create a new instance of \Miao\Log\Writer\Mock
     * @param  array|\Miao\Config $config
     * @return \Miao\Log\Writer\Mock
     * @throws \Miao\Log\Exception
     */
    static public function factory( $config )
    {
        return new self();
    }
}