<?php
class Miao_Form extends Miao_Form_Control
{

	protected $_action = '/';

	protected $_method = 'POST';

	protected $_enctype = 'multipart/form-data';

	protected $_attributes = array();

	protected $_isValid = true;

	/**
	 *
	 * @var array Miao_Form_Control
	 */
	protected $_controls = array();

	/**
	 * @return the $_action
	 */
	public function getAction()
	{
		return $this->_action;
	}

	/**
	 * @param string $action
	 */
	public function setAction( $action )
	{
		$this->_action = $action;
	}

	/**
	 * @return the $_type
	 */
	public function getMethod()
	{
		return $this->_method;
	}

	/**
	 * @param string $type
	 */
	public function setMethod( $method )
	{
		$this->_method = $method;
	}

	/**
	 * @return the $_enctype
	 */
	public function getEnctype()
	{
		return $this->_enctype;
	}

	/**
	 * @param string $enctype
	 */
	public function setEnctype( $enctype )
	{
		$this->_enctype = $enctype;
	}

	/**
	 * @return the $_controls
	 */
	public function getControls()
	{
		return $this->_controls;
	}

	public function addControl( Miao_Form_Control $obj )
	{
		$index = $obj->getName();
		if ( array_key_exists( $index, $this->_controls ) )
		{
			$msg = sprintf( 'Control with name (%s) already exists', $index );
			throw new Miao_Form_Exception( $msg );
		}
		$this->_controls[ $index ] = $obj;
	}

	public function __construct( $id, $action = '/', array $attributes = array() )
	{
		$this->_exceptAttrMap = array( 'id', 'method', 'action', 'enctype' );
		$this->setAction( $action );

		parent::__construct( $id, $attributes );
	}

	public function __get( $name )
	{
		if ( !array_key_exists( $name, $this->_controls ) )
		{
			$msg = sprintf( 'Ivalid control name (%s), control does not exists', $name );
			throw new Miao_Form_Exception( $msg );
		}
		$result = $this->_controls[ $name ];
		return $result;
	}

	public function load( $data )
	{
		$data = self::getHtmlName( $data );

		foreach ( $data as $key => $value )
		{
			if ( array_key_exists( $key, $this->_controls ) )
			{
				$this->_controls[ $key ]->setValue( $value );
			}
		}
	}

	public function getValues()
	{
		$result = array();
		foreach ( $this->_controls as $name => $control )
		{
			$result[ $name ] = $control->getValue();
		}
		return $result;
	}

	public function clearValue()
	{
		foreach ( $this->_controls as $control )
		{
			$control->clearValue();
		}
	}

	/**
	 * @param array $data from data
	 */
	public function isValid( array $data = array() )
	{
		if ( !empty( $data ) )
		{
			$this->load( $data );
			$result = true;
			foreach ( $this->_controls as $control )
			{
				$result = $control->validate() && $result;
			}
			$this->_isValid = $result;
		}
		return $this->_isValid;
	}

	public function begin()
	{
		$pieces = array();
		$pieces[] = '<form';
		$pieces[] = sprintf( 'name="%s"', $this->getName() );
		$pieces[] = sprintf( 'action="%s"', $this->getAction() );
		$pieces[] = sprintf( 'method="%s"', $this->getMethod() );
		$pieces[] = sprintf( 'enctype="%s"', $this->getEnctype() );
		$pieces[] = $this->_renderAttributes();

		$result = trim( implode( ' ', $pieces ) ) . '>';
		return $result;
	}

	public function end()
	{
		$result = '</form>';
		return $result;
	}

	/**
	 *
	 * @param unknown_type $name
	 * @param array $attributes
	 * @return Miao_Form_Control_Text
	 */
	public function addText( $name, array $attributes = array() )
	{
		$obj = new Miao_Form_Control_Text( $name, $attributes );
		$this->addControl( $obj );
		return $obj;
	}

	public function addTextArea( $name, array $attributes = array() )
	{
		$obj = new Miao_Form_Control_Textarea( $name, $attributes );
		$this->addControl( $obj );
		return $obj;
	}

	public function addSubmit( $name, array $attributes = array() )
	{
		$obj = new Miao_Form_Control_Submit( $name, $attributes );
		$this->addControl( $obj );
		return $obj;
	}

	public function addButton( $name, array $attributes = array() )
	{
		$obj = new Miao_Form_Control_Button( $name, $attributes );
		$this->addControl( $obj );
		return $obj;
	}

	public function addReset( $name, array $attributes = array() )
	{
		$obj = new Miao_Form_Control_Reset( $name, $attributes );
		$this->addControl( $obj );
		return $obj;
	}

	/**
	 *
	 * @param string $name
	 * @param array $attributes
	 * @param Miao_Form_Control $captcha
	 * @return Ambigous <Miao_Form_Control, Miao_Form_Control_Captcha>
	 */
	public function addCaptcha( $name, array $attributes = array(), Miao_Form_Control $captcha = null )
	{
		$obj = $captcha;
		if ( is_null( $obj ) )
		{
			$obj = new Miao_Form_Control_Captcha( $name, $attributes );
		}
		$this->addControl( $obj );
		return $obj;
	}

	public function render()
	{
		$pieces = array();
		foreach ( $this->getControls() as $control )
		{
			$pieces[] = $control->render();
		}
		$result = implode( chr( 10 ), $pieces );

		return $result;
	}

	static public function getHtmlName( $data )
	{
		$result = array();
		self::_getHtmlName( $data, $result );
		return $result;
	}

	static protected function _getHtmlName( $data, &$result, array & $keys = array() )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $key => $value )
			{
				$newKeys = $keys;
				$newKeys[] = $key;
				self::_getHtmlName( $value, $result, $newKeys );
			}
		}
		else
		{
			$name = array_shift( $keys );
			if ( !empty( $keys ) )
			{
				$name .= '[' . implode( '][', $keys ) . ']';
			}
			$result[ $name ] = $data;
		}
	}
}