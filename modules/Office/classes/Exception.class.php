<?php
/**
 * UniPg
 * @package Office
 */

/**
 * Base class for Uniora-Front-Office-exceptions.
 *
 * @copyright RBC
 * @package Office
 */

class Miao_Office_Exception extends Exception
{
	public function __construct( $message, $code = 0, Exception $previous = NULL )
	{
		if ( !headers_sent() )
		{
			header( "HTTP/1.0 404 Not Found" );
		}
		parent::__construct( $message, $code, $previous );
	}
}
