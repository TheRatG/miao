<?php
/**
 * Search config
 * Get specific section by class name
 * Get instance
 *
 * @author vpak
 *
 */
class Miao_Config_Instance
{
	static public function get( $className, $paramSection = '__construct' )
	{
		$configObj = Miao_Config::Libs( $className );
		$params = $configObj->get( $paramSection );

		$rc = new ReflectionClass( $className );
		$result = $rc->newInstanceArgs( $params );

		return $result;
	}
}