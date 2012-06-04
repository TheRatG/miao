<?php
class Miao_Form extends Miao_Form_Control
{

	protected $_action = '/';

	protected $_method = 'POST';

	protected $_enctype = 'multipart/form-data';

	protected $_attributes = array();

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
		$index = $obj->getId();
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

	public function loadValue( $data )
	{
		foreach ( $data as $key => $value )
		{
			if ( array_key_exists( $key, $this->_controls ) )
			{
				$this->_controls[ $key ]->setValue( $value );
			}
		}
	}

	public function begin()
	{
		$pieces = array();
		$pieces[] = '<form';
		$pieces[] = sprintf( 'id="%s"', $this->getId() );
		$pieces[] = sprintf( 'name="%s"', $this->getId() );
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

	public function addText( $name, array $attributes = array() )
	{
		$obj = new Miao_Form_Control_Text( $name, $attributes );
		$this->addControl( $obj );
		return $obj;
	}

	public function addTextArea()
	{
	}

	public function addSubmit()
	{
	}

	public function addButton()
	{
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
}