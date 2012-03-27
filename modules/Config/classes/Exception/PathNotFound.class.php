<?php
class Miao_Config_Exception_PathNotFound extends Miao_Config_Exception
{
	public function __construct( $path )
	{
		$msg = sprintf( 'Path "%s" not found', $path );
		parent::__construct( $msg );
	}
}