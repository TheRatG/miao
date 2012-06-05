<?php
abstract class Miao_Office_DataHelper_FormController
{

	private $_fid = '';

	/**
	 *
	 * @var Miao_Form
	 */
	protected $_form;

	/**
	 *
	 * @var bool
	 */
	protected $_isRedirect;

	/**
	 *
	 * @var bool
	 */
	protected $_isValid;

	static protected function _getInstance( $className )
	{
		$index = 'frm::' . $className;
		if ( !Miao_Registry::isRegistered( $index ) )
		{
			$instance = new $className();
			Miao_Registry::set( $index, $instance );
		}
		return $instance;
	}

	/**
	 * @return Miao_Form
	 */
	abstract public function buildForm();

	/**
	 * @return Miao_Office_DataHelper_FormController
	*/
	abstract static public function getInstance();

	/**
	 *
	 * @return Miao_Form
	*/
	public function getForm()
	{
		return $this->_form;
	}

	public function isRedirect()
	{
		return $this->_isRedirect;
	}

	public function isValid()
	{
		return $this->_isValid;
	}

	public function save()
	{
		$session = Miao_Session::getInstance();
		$session->saveObject( $this->_fid, $this->_form );
	}

	protected function _generateFid()
	{
		$session = Miao_Session::getInstance();
		$this->_fid = md5( $session->getSessionId() . '_form_' . get_class( $this ) );
	}

	protected function __construct()
	{
		$this->_generateFid();

		$session = Miao_Session::getInstance();
		$form = $session->loadObject( $this->_fid, null, true );
		$session->saveObject( $this->_fid, null );
		if ( is_null( $form ) )
		{
			$this->_form = $this->buildForm();
		}
		else
		{
			$this->_form = $form;
			$this->_isRedirect = true;

			$request = Miao_Office_Request::getInstance();
			$data = $request->getVars();
			$this->_isValid = $this->_form->isValid( $data );
		}
		$this->save();
	}

	protected function __clone()
	{
	}
}