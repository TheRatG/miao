<?php
abstract class Miao_Form_Controller
{
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
		$index = 'frm::' . $className;
		if ( !Miao_Registry::isRegistered( $index ) )
		{
			$instance = new $className();
			Miao_Registry::set( $index, $instance );
		}
		else
		{
			$instance = Miao_Registry::get( $index );
		}
		return $instance;
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