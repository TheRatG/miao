<?php
/**
 * @author vpak
 * @date 2013-08-13 16:24:31
 */

namespace Miao\Log\Filter;

class Suppress extends \Miao\Log\Filter\AbstractFilter implements \Miao\Log\Filter\FilterInterface
{
    /**
     * @var boolean
     */
    protected $_accept = true;

    /**
     * This is a simple boolean filter.
     * Call suppress(true) to suppress all log events.
     * Call suppress(false) to accept all log events.
     * @param  boolean $suppress  Should all log events be suppressed?
     * @return  void
     */
    public function suppress( $suppress )
    {
        $this->_accept = ( !$suppress );
    }

    /**
     * Returns TRUE to accept the message, FALSE to block it.
     * @param  array $event    event data
     * @return boolean            accepted?
     */
    public function accept( $event )
    {
        return $this->_accept;
    }

    /**
     * Create a new instance of \Miao\Log\Filter\Suppress
     * @param  array|\Miao\Config $config
     * @return \Miao\Log\Filter\Suppress
     * @throws \Miao\Log\Exception
     */
    static public function factory( $config )
    {
        return new self();
    }
}