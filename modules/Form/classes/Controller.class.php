<?php
abstract class Miao_Form_Controller
{

	private $_fid = '';

	private $_session;

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
				$this->_save();
			}
		}
		$result = $this->_isRedirect;
		return $result;
	}

	public function isValid()
	{
		return $this->_isValid;
	}

	protected function _save()
	{
		$session = $this->_session;
		$data = array(
			'isRedirect' => $this->isRedirect(),
			'form' => $this->_form );
		$session[ $this->_fid ] = $data;
	}

	protected function _clear()
	{
		$session = $this->_session;
		$session[ $this->_fid ] = null;
	}

	protected function _load()
	{
		$session = $this->_session;
		$res = null;
		if ( $session->offsetExists( $this->_fid ) )
		{
			$res = $session[ $this->_fid ];
		}

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

	protected function _init()
	{
		$this->_generateFid();
		$this->_load();
		$this->_clear();

		if ( is_null( $this->_form ) )
		{
			$this->_form = $this->buildForm();
			$this->_loadData();
			$this->_save();
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
				$this->_clear();
			}
			else
			{
				$this->_loadData();
				$this->_save();
			}
		}
	}

	protected function _loadData()
	{
		$request = Miao_Office_Request::getInstance();
		if ( 'POST' === $request->getMethod() )
		{
			$data = $request->getVars();
			$this->_isValid = $this->_form->isValid( $data );
		}
	}

	protected function _generateFid( $additionalFid = '' )
	{
		$this->_fid = md5( session_id() . '_form_' . get_class( $this ) . $additionalFid );
	}

	protected function __construct()
	{
		$this->_session = Miao_Session::getNamespace( __CLASS__ );
		$this->_init();
	}

	protected function __clone()
	{
	}
}