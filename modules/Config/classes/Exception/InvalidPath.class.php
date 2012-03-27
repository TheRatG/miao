<?php
class Miao_Config_Exception_InvalidPath extends Miao_Config_Exception
{
	public function __construct( $path, $reason )
	{
		$msg = sprintf( 'Invalid path "%s": %s', $path, $reason );
		parent::__construct( $msg );
	}
}