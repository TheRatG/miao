<?php
/**
 * @author vpak
 * @date 2013-08-13 16:24:20
 */

namespace Miao\Log\Filter;

class Priority extends \Miao\Log\Filter\AbstractFilter implements \Miao\Log\Filter\FilterInterface
{
    /**
     * @var integer
     */
    protected $_priority;

    /**
     * @var string
     */
    protected $_operator;

    /**
     * Filter logging by $priority.  By default, it will accept any log
     * event whose priority value is less than or equal to $priority.
     * @param  integer $priority  Priority
     * @param  string $operator  Comparison operator
     * @throws \Miao\Log\Exception
     */
    public function __construct( $priority, $operator = null )
    {
        if ( !is_integer( $priority ) )
        {
            throw new \Miao\Log\Exception( 'Priority must be an integer' );
        }

        $this->_priority = $priority;
        $this->_operator = is_null( $operator ) ? '<=' : $operator;
    }

    /**
     * Create a new instance of \Miao\Log\Filter\Priority
     * @param  array|\Miao\Config $config
     * @return \Miao\Log\Filter\Priority
     * @throws \Miao\Log\Exception
     */
    static public function factory( $config )
    {
        $config = self::_parseConfig( $config );
        $config = array_merge( array( 'priority' => null, 'operator' => null ), $config );

        // Add support for constants
        if ( !is_numeric( $config[ 'priority' ] ) && isset( $config[ 'priority' ] )
            && defined(
                $config[ 'priority' ]
            )
        )
        {
            $config[ 'priority' ] = constant( $config[ 'priority' ] );
        }

        return new self( ( int )$config[ 'priority' ], $config[ 'operator' ] );
    }

    /**
     * Returns TRUE to accept the message, FALSE to block it.
     * @param  array $event    event data
     * @return boolean            accepted?
     */
    public function accept( $event )
    {
        return version_compare( $event[ 'priority' ], $this->_priority, $this->_operator );
    }
}