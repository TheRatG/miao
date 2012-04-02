<?php
/**
 * Miao
 * @package Office
 */


/**
 * Формирует представление данных для классов Miao_Office_View и Miao_Office_ViewBlock.
 * Реализует паттерн Singleton
 *
 * @package Office
 *
 * @abstract
 */
abstract class Miao_Office_ViewHelper
{

	/**
	 * Массив экзепляров классов-хелперов.
	 *
	 * @var array
	 */
	static private $_instanse = array();

	/**
	 * Создает экземпляр класса по первому параметру и сохраняет в список по указанному имени,
	 * если имя не указано, то используется имя класса.
	 *
	 * @param string $instance_class
	 * @param string $instanse_name
	 * @return object
	 *
	 * @static
	 */
	static protected function _getInstance( $instance_class, $instanse_name = '' )
	{
		if ( empty( $instanse_name ) )
		{
			$instanse_name = $instance_class;
		}
		else
		{
			$instanse_name = $instance_class . '::' . $instanse_name;
		}

		if ( !isset( self::$_instanse[ $instanse_name ] )
			|| is_null( self::$_instanse[ $instanse_name ] )
			)
		{
			self::$_instanse[ $instanse_name ] = new $instance_class();
		}
		return self::$_instanse[ $instanse_name ];
	}

	/**
	 * Конструктор класса.
	 *
	 */
	protected function __construct()
	{
		$this->_initialize();
	}

	/**
	 * Запрещаем клонировать.
	 *
	 * @final
	 */
	final protected function __clone()
	{
	}

	/**
	 * Вызывается конструктором
	 *
	 * @abstract
	 */
	abstract protected function _initialize();
}
