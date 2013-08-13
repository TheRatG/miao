<?php
/**
 * @author vpak
 * @date 2013-08-13 16:23:04
 */

namespace Miao\Log\Writer;

abstract class AbstractWriter implements \Miao\Log\FactoryInterface
{
    /**
     * @var array of \Miao\Log\Filter\FilterInterface
     */
    protected $_filters = array();

    /**
     * Formats the log message before writing.
     * @var \Miao\Log\Formatter\FormatterInterface
     */
    protected $_formatter;

    /**
     * Add a filter specific to this writer.
     * @param  \Miao\Log\Filter\FilterInterface $filter
     * @return void
     * @throws \Miao\Log\Exception
     */
    public function addFilter( $filter )
    {
        if ( is_integer( $filter ) )
        {
            $filter = new \Miao\Log\Filter\Priority( $filter );
        }

        if ( !$filter instanceof \Miao\Log\Filter\FilterInterface )
        {
            throw new \Miao\Log\Exception( 'Invalid filter provided' );
        }

        $this->_filters[ ] = $filter;
    }

    /**
     * Log a message to this writer.
     * @param  array $event  log data event
     * @return void
     */
    public function write( $event )
    {
        foreach ( $this->_filters as $filter )
        {
            if ( !$filter->accept( $event ) )
            {
                return;
            }
        }
        // exception occurs on error
        $this->_write( $event );
    }

    /**
     * Set a new formatter for this writer
     * @param  \Miao\Log\Formatter\FormatterInterface $formatter
     * @return void
     */
    public function setFormatter( \Miao\Log\Formatter\FormatterInterface $formatter )
    {
        $this->_formatter = $formatter;
    }

    /**
     * Perform shutdown activities such as closing open resources
     * @return void
     */
    public function shutdown()
    {
    }

    /**
     * Write a message to the log.
     * @param  array $event  log data event
     * @return void
     */
    abstract protected function _write( $event );

    /**
     * Validate and optionally convert the config to array
     * @param  array|\Miao\Config $config \Miao\Config or Array
     * @return array
     * @throws \Miao\Log\Exception
     */
    static protected function _parseConfig( $config )
    {
        if ( !is_array( $config ) )
        {
            throw new \Miao\Log\Exception( 'Configuration must be an array' );
        }

        return $config;
    }
}