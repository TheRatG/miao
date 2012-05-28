<?php
class Miao_Env
{
	static public function defaultRegister()
	{
		$configDefault = array(
			'error_level' => '30711',
			'default_timezone' => 'Europe/Moscow',
			'unregister_globals' => '1',
			'strip_global_slashes' => '1',
			'umask' => '0',
			'upload_tmp_dir' => '/tmp' );

		if ( Miao_Config::checkConfig( __CLASS__ ) )
		{
			$config = Miao_Config::Libs( __CLASS__ );
		}
		else
		{
			$config = new Miao_Config_Base( $configDefault );
		}
		$initialazer = new Miao_Env_Initializer( $config );
		$initialazer->run();
	}
	static public function register( array $config )
	{
		$config = new Miao_Config_Base( $config );
		$initialazer = new Miao_Env_Initializer( $config );
		$initialazer->run();
	}
}