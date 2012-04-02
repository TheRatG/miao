<?php
/**
 * UniPg
 * @package TemplatesEngine
 */

/**
 * Exception-class used in TemplatesEngine
 *
 * @throws if template-file can't be open
 * @package TemplatesEngine
 * @subpackage TemplatesEngine_Exception
 */
class Miao_TemplatesEngine_Exception_OnFailFileOpen extends Miao_TemplatesEngine_Exception
{
	/**
	 * @param sting $fileName имя файла
	 * @param string $mode режим открытия файла
	 */
	public function __construct( $fileName, $mode )
	{
		parent::__construct( 'Can\'t open file "' . $fileName . '" with opening mode "' . $mode . '"' );
	}
}
