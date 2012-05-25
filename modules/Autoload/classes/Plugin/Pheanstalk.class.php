<?php
class Miao_Autoload_Plugin_Pheanstalk extends Miao_Autoload_Plugin
{
	public function __construct( $libPath )
	{
		parent::__construct( $libPath );

		require_once $libPath . '/pheanstalk_init.php';
	}

	public function getFilenameByClassName( $className )
	{

	}
}