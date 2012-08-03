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

	private $_clearNumber = 1;

	/**
	 *
	 * @param string $className
	 * @return Miao_Form_Controller
	 */
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
			if ( $this->_isRedirect )
			{
				$this->save();
			}
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
		$data = array(
			'isRedirect' => $this->isRedirect(),
			'form' => $this->_form );
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
		else if ( isset( $res[ 'form' ] ) )
		{
			$this->_form = $res[ 'form' ];
			$this->_isRedirect = $res[ 'isRedirect' ];
		}
	}

	public function _init()
	{
		$this->_generateFid();
		$this->load();
		$this->clear();

		if ( is_null( $this->_form ) )
		{
			$this->_form = $this->buildForm();
			$this->save();
		}
		else
		{
			if ( $this->isRedirect() )
			{
				$this->_isRedirect = false;
				$this->_isValid = $this->_form->isValid();
				if ( $this->_isValid )
				{
					$this->_form->clearValue();
				}
				$this->clear();
			}
			else
			{
				$request = Miao_Office_Request::getInstance();
				if ( 'POST' === $request->getMethod() )
				{
					$data = $request->getVars();
					$this->_isValid = $this->_form->isValid( $data );
				}
				$this->save();
			}
		}
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