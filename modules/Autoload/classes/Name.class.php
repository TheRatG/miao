<?php
class Miao_Autoload_Name
{
	const T_CLASS = 1;
	const T_MODULE = 2;
	const T_LIB = 4;

	private $_type;

	private $_name;
	private $_lib;
	private $_module;
	private $_class;
	private $_isTest = false;
	private $_cnt;

	private $_path;

	/**
	 * @return the $_type
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * @return the $_name
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @param field_type $name
	 */
	public function setName( $name )
	{
		$this->_name = $name;
	}

	/**
	 * @return the $_lib
	 */
	public function getLib()
	{
		return $this->_lib;
	}

	/**
	 * @param field_type $lib
	 */
	public function setLib( $lib )
	{
		$this->_lib = $lib;
	}

	/**
	 * @return the $_module
	 */
	public function getModule()
	{
		return $this->_module;
	}

	/**
	 * @param field_type $module
	 */
	public function setModule( $module )
	{
		$this->_module = $module;
	}

	public function getClass()
	{
		return $this->_class;
	}

	/**
	 * @return the $_isTest
	 */
	public function isTest()
	{
		return $this->_isTest;
	}

	/**
	 * @return the $_cnt
	 */
	public function getCnt()
	{
		return $this->_cnt;
	}

	public function parse( $name )
	{
		if ( empty( $name ) )
		{
			throw new Miao_Autoload_Exception_InvalidClassName( $name, 'empty string' );
		}

		$ar = explode( '_', $name );

		$this->setName( $name );
		$this->setLib( $ar[ 0 ] );
		$module = '';
		if ( isset( $ar[ 1 ] ) )
		{
			$module = $ar[ 1 ];
		}
		if ( isset( $ar[ 2 ] ) )
		{
			$this->_class = implode( '_', array_slice( $ar, 2 ) );
		}
		$this->setModule( $module );
		$this->_cnt = count( $ar );

		switch ( $this->_cnt )
		{
			case 1:
				$this->_type = self::T_LIB;
				break;
			case 2:
				$this->_type = self::T_MODULE;
				break;
			default:
				$this->_type = self::T_CLASS;
				break;
		}

		if ( 'Test' === $ar[ $this->_cnt -1 ] )
		{
			$this->_isTest = true;
		}
	}

	public function toArray()
	{
		$result = array(
			'type' => $this->getType(),
			'name' => $this->getName(),
			'lib' => $this->getLib(),
			'module' => $this->getModule(),
			'class' => $this->getClass() );
		return $result;
	}
}