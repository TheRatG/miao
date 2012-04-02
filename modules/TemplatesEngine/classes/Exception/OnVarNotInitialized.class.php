<?php
/**
 * UniPg
 * @package TemplatesEngine
 */

/**
 * Exception-class used in TemplatesEngine
 *
 * @throws if in template was called non-initialized template-variable
 * @package TemplatesEngine
 * @subpackage TemplatesEngine_Exception
 */
class Miao_TemplatesEngine_Exception_OnVarNotInitialized extends Miao_TemplatesEngine_Exception
{
	/**
	 * @param string $templateVarName имя переменной
	 */
	public function __construct( $templateVarName )
	{
		parent::__construct( 'Template variable with name "' . $templateVarName . '" wasn\'t initialized' );
	}
}
