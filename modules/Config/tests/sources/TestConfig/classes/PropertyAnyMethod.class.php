<?php
class Miao_TestConfig_PropertyAnyMethod
{
	private $_constrPropOne;
	private $_constrPropTwo;

	private $_propOnew;
	private $_propTwo;
	private $_propThree;


	public function __construct( $constrPropOne, $constrPropTwo )
	{

	}
	/**
	 * @return the $_constrPropOne
	 */
	public function getConstrPropOne()
	{
		return $this->_constrPropOne;
	}

	/**
	 * @param field_type $_constrPropOne
	 */
	public function setConstrPropOne( $_constrPropOne )
	{
		$this->_constrPropOne = $_constrPropOne;
	}

	/**
	 * @return the $_constrPropTwo
	 */
	public function getConstrPropTwo()
	{
		return $this->_constrPropTwo;
	}

	/**
	 * @param field_type $_constrPropTwo
	 */
	public function setConstrPropTwo( $_constrPropTwo )
	{
		$this->_constrPropTwo = $_constrPropTwo;
	}

	/**
	 * @return the $_propOnew
	 */
	public function getPropOnew()
	{
		return $this->_propOnew;
	}

	/**
	 * @param field_type $_propOnew
	 */
	public function setPropOnew( $_propOnew )
	{
		$this->_propOnew = $_propOnew;
	}

	/**
	 * @return the $_propTwo
	 */
	public function getPropTwo()
	{
		return $this->_propTwo;
	}

	/**
	 * @param field_type $_propTwo
	 */
	public function setPropTwo( $_propTwo )
	{
		$this->_propTwo = $_propTwo;
	}

	/**
	 * @return the $_propThree
	 */
	public function getPropThree()
	{
		return $this->_propThree;
	}

	/**
	 * @param field_type $_propThree
	 */
	public function setPropThree( $_propThree )
	{
		$this->_propThree = $_propThree;
	}
}