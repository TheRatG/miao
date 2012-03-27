<?php
class Miao_Autoload_Exception_InvalidClassName extends Miao_Autoload_Exception
{
	public function __construct( $className, $reason )
	{
		$msg = sprintf( 'Invalid class name "%s": %s', $className, $reason );
		parent::__construct( $msg );
	}
}
