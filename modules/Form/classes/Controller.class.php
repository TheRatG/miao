<?php
abstract class Miao_Form_Controller
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

	private $_clearNumber = 3;

	private $_loadCnt = 0;

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
	 * @return Miao_Form_Controller
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

	public function isRedirect( $val = null )
	{
		if ( !is_null( $val ) )
		{
			$this->_isRedirect = ( bool ) $val;
		}
		$result = $this->_isRedirect;
		return $result;
	}

	public function isValid()
	{
		return $this->_isValid;
	}

	public function save()
	{
		$session = Miao_Session::getInstance();
		$this->_loadCnt++;
		$data = array( 'loadCnt' => $this->_loadCnt, 'form' => $this->_form );
		$session->saveObject( $this->_fid, $data );
	}

	public function clear()
	{
		$session = Miao_Session::getInstance();
		$session->saveObject( $this->_fid, null );
	}

	public function load()
	{
		$session = Miao_Session::getInstance();
		$res = $session->loadObject( $this->_fid, null, true );
		if ( is_null( $res ) )
		{
			$form = $res;
		}
		else if ( isset( $res[ 'loadCnt' ] ) && isset( $res[ 'form' ] ) )
		{
			list( $this->_loadCnt, $this->_form ) = $res;
			$this->_loadCnt = $res[ 'loadCnt' ];
			$this->_form = $res[ 'form' ];
			if ( $this->_loadCnt >= $this->_clearNumber )
			{
				$this->_form = null;
				$this->_loadCnt = 0;
			}
		}
		return $form;
	}

	public function _init()
	{
		$this->_generateFid();
		$this->load();
		$this->clear();

		if ( is_null( $this->_form ) )
		{
			$this->_form = $this->buildForm();
		}
		else
		{
			$request = Miao_Office_Request::getInstance();
			$data = $request->getVars();
			$this->_isValid = $this->_form->isValid( $data );
		}
		$this->save();
	}

	protected function _generateFid()
	{
		$session = Miao_Session::getInstance();
		$this->_fid = md5( $session->getSessionId() . '_form_' . get_class( $this ) );
	}

	protected function __construct()
	{
		$this->_init();
	}

	protected function __clone()
	{
	}
}