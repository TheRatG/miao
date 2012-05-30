<?php
abstract class Miao_Session_Handler
{
	/**
	 *
	 * @return Miao_Session_Handler
	 */
	static public function factory()
	{
		$config = Miao_Config::Libs( __CLASS__ );
		$className = $config->get( 'type', 'none' );
		$className = 'Miao_Session_Handler_' . ucfirst( $className );
		$result = new $className();
		return $result;
	}

	abstract public function init();
}