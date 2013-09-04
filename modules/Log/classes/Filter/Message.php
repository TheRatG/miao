<?php
/**
 * @author vpak
 * @date 2013-08-13 16:24:09
 */

namespace Miao\Log\Filter;

class Message extends \Miao\Log\Filter\AbstractFilter implements \Miao\Log\Filter\FilterInterface
{
    /**
     * @var string
     */
    protected $_regexp;

    /**
     * Filter out any log messages not matching $regexp.
     * @param  string $regexp     Regular expression to test the log message
     * @throws \Miao\Log\Exception
     */
    public function __construct( $regexp )
    {
        if ( @preg_match( $regexp, '' ) === false )
        {
            throw new \Miao\Log\Exception( "Invalid regular expression '$regexp'" );
        }
        $this->_regexp = $regexp;
    }

    /**
     * Create a new instance of \Miao\Log\Filter\Message
     * @param  array|\Miao\Config $config
     * @return \Miao\Log\Filter\Message
     * @throws \Miao\Log\Exception
     */
    static public function factory( $config )
    {
        $config = self::_parseConfig( $config );
        $config = array_merge( array( 'regexp' => null ), $config );

        return new self( $config[ 'regexp' ] );
    }

    /**
     * Returns TRUE to accept the message, FALSE to block it.
     * @param  array $event    event data
     * @return boolean            accepted?
     */
    public function accept( $event )
    {
        return preg_match( $this->_regexp, $event[ 'message' ] ) > 0;
    }
}