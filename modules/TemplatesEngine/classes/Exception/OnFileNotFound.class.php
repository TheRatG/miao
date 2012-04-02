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
class Miao_TemplatesEngine_Exception_OnFileNotFound extends Miao_TemplatesEngine_Exception
{
	/**
	 * @param string $fileName имя файла 
	 */
	public function __construct( $fileName )
	{
		parent::__construct( 'File not found: path = "' . $fileName . '"' );
	}
}
