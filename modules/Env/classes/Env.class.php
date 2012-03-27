<?php
class Miao_Env
{
	static public function register( array $config )
	{
		$config = new Miao_Config_Base( $config );
		$initialazer = new Miao_Env_Initializer( $config );
		$initialazer->run();
	}
}