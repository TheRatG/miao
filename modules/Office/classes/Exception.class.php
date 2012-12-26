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
		parent::__construct( $message, $code, $previous );
	}
}
