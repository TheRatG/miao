<?php
/**
 * UniPg
 * @package TemplatesEngine
 */

/**
 * Exception-class used in TemplatesEngine
 *
 * @throws if template-file not found
 * @package TemplatesEngine
 * @subpackage TemplatesEngine_Exception
 */
class Miao_TemplatesEngine_Exception_OnVariableNotFound extends Miao_TemplatesEngine_Exception
{
	/**
	 * @param string $fileName имя файла
	 */
	public function __construct( $varName )
	{
		$message = sprintf( 'Tmp variable (%s) not found', $varName );
		parent::__construct( $message );
	}
}