<?php
class Miao_Log_Filter_Message extends Miao_Log_Filter_Abstract
{
	/**
	 * @var string
	 */
	protected $_regexp;

	/**
	 * Filter out any log messages not matching $regexp.
	 *
	 * @param  string  $regexp     Regular expression to test the log message
	 * @throws Miao_Log_Exception
	 */
	public function __construct( $regexp )
	{
		if ( @preg_match( $regexp, '' ) === false )
		{
			throw new Miao_Log_Exception( "Invalid regular expression '$regexp'" );
		}
		$this->_regexp = $regexp;
	}

	/**
	 * Create a new instance of Miao_Log_Filter_Message
	 *
	 * @param  array|Miao_Config $config
	 * @return Miao_Log_Filter_Message
	 * @throws Miao_Log_Exception
	 */
	static public function factory( $config )
	{
		$config = self::_parseConfig( $config );
		$config = array_merge( array( 'regexp' => null ), $config );

		return new self( $config[ 'regexp' ] );
	}

	/**
	 * Returns TRUE to accept the message, FALSE to block it.
	 *
	 * @param  array    $event    event data
	 * @return boolean            accepted?
	 */
	public function accept( $event )
	{
		return preg_match( $this->_regexp, $event[ 'message' ] ) > 0;
	}
}
