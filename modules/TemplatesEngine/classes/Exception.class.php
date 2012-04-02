<?php
/**
 * UniPg
 * @package TemplatesEngine
 */

/**
 * Base class for for all exception-classes used in TemplatesEngine
 * @package TemplatesEngine
 */
class Miao_TemplatesEngine_Exception extends Exception
{
	protected $_defaultMessage = 'Ошибка шаблонизатора';

	/**
	 * @param string $message сообщение
	 */
	public function __construct( $message )
	{
		parent::__construct( $message );
	}
}
