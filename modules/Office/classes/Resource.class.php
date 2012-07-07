<?php
abstract class Miao_Office_Resource
{
	/**
	 * @var string
	 */
	protected $_content;

	/**
	 *
	 * @var Miao_Office
	 */
	protected $_frontOffice;

	protected $_access = false;

	/**
	 * @return Miao_Office
	 */
	public function getOffice()
	{
		return $this->_frontOffice;
	}

	/**
	 * @param Miao_Office $frontOffice
	 */
	public function setOffice( $frontOffice )
	{
		$this->_frontOffice = $frontOffice;
	}

	/**
	 * @param $content the $_content to set
	 */
	public function setContent( $content )
	{
		assert( is_string( $content ) );
		$this->_content = $content;
	}

	/**
	 * @return the $_response
	 */
	public function getContent()
	{
		return $this->_content;
	}

	public function sendHeader()
	{
		$result = $this->getOffice()->getHeader()->send();
		return $result;
	}

	protected function _checkExistsOperationObject()
	{
		$action = $this->_frontOffice->getAction();
		$view = $this->_frontOffice->getView();
		$viewBlock = $this->_frontOffice->getViewBlock();

		$result = false;
		if ( $action || $view || $viewBlock )
		{
			$result = TRUE;
		}
		return $result;
	}

	/**
	 * Инициализация
	 *
	 */
	abstract protected function _initialize();

	/**
	 * Отправка ответа
	 *
	 */
	abstract public function sendResponse();
}