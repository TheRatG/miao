<?php
abstract class Miao_Office_Action
{
	/**
	 *
	 * @var Miao_Office
	 */
	protected $_office;

	public function __construct()
	{
		$this->_init();
	}

	/**
	 *
	 * @param Miao_Office $office
	 */
	public function setOffice( Miao_Office $office )
	{
		$this->_office = $office;
	}

	/**
	 *
	 * @return Miao_Office
	 */
	public function getOffice()
	{
		return $this->_office;
	}

	abstract public function execute();

	protected function _init()
	{

	}
}