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

		$config = Miao_Config::Libs( __CLASS__, false );
		if ( $config )
		{
			$config = Miao_Config::Libs( __CLASS__ );
		}
		else
		{
			$config = new Miao_Config_Base( $configDefault );
		}
		$result = self::_run( $config );
		return $result;
	}

	static public function register( array $config = array() )
	{
		if ( empty( $config ) )
		{
			$result = self::defaultRegister();
		}
		else
		{
			$config = new Miao_Config_Base( $config );
			$result = self::_run( $config );
		}

		return $result;
	}

	static protected function _run( Miao_Config_Base $config )
	{
		$initialazer = new Miao_Env_Initializer( $config );
		$result = $initialazer->run();
		return $result;
	}
}