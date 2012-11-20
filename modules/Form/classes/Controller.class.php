<?php
abstract class Miao_Form_Controller
{
	static private $_instance;

	/**
	 *
	 * @var Miao_Form
	 */
	protected $_form;
	protected $_errors = array();

	/**
	 *
	 * @var bool
	 */
	protected $_isValid;

	/**
	 *
	 * @param string $className
	 * @return Miao_Form_Controller
	 */
	final static public function getInstance()
	{
		$className = get_called_class();
		if ( is_null(self::$_instance))
		{
			self::$_instance = new $className();
		}
		return self::$_instance;
	}

	/**
	 * @return Miao_Form
	 */
	abstract public function buildForm();

	/**
	 *
	 * @return Miao_Form
	*/
	public function getForm()
	{
		return $this->_form;
	}

	public function isValid( $run = false )
	{
		if ( $run )
		{
			$request = Miao_Office_Request::getInstance();
			$data = $request->getVars();
			$this->_isValid = $this->_form->isValid( $data );
		}
		return $this->_isValid;
	}

	public function addError( $message )
	{
		$this->_errors[] = $message;
	}

	public function getErrors()
	{
		return $this->_errors;
	}

	protected function _init()
	{
		$form = $this->buildForm();
		$this->_form = $form;
	}

	protected function __construct()
	{
		$this->_init();
	}

	protected function __clone()
	{
	}
}