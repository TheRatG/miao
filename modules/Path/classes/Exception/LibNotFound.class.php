<?php
class Miao_Path_Exception_LibNotFound extends Miao_Path_Exception
{
	public function __construct( $libName )
	{
		$msg = sprintf( 'Lib "%s" not found', $libName );
		parent::__construct( $msg );
	}
}
