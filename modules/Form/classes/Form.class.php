<?php
/**
 * Miao_From
 * <code>
 * $form = new Miao_Form( 'user' );
 * $form->addText('username');
 * </code>
 */
class Miao_Form extends Miao_Form_Control
{
	protected $_action = '/';
	protected $_method = 'POST';
	protected $_enctype = 'multipart/form-data';
	protected $_attributes = array();
	protected $_isValid = true;

	/**
	 *
	 * @var Miao_Form_Control[]
	 */
	protected $_controls = array();

	/**
	 * @return string the $_action
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
	 * @return string the $_type
	 */
	public function getMethod()
	{
		return $this->_method;
	}

    /**
     * @param $method
     */
    public function setMethod( $method )
	{
		$this->_method = $method;
	}

	/**
	 * @return string the $_enctype
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
	 * @return Miao_Form_Control[]
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

	public function __construct( $id = '', $action = '/', array $attributes = array() )
	{
		$this->_exceptAttrMap = array( 'id', 'method', 'action', 'enctype' );
		$this->setAction( $action );
		parent::__construct( $id, $attributes );
	}

	public function __get( $name )
	{
		if ( !array_key_exists( $name, $this->_controls ) )
		{
			$msg = sprintf(
				'Ivalid control name (%s), control does not exists', $name );
			throw new Miao_Form_Exception( $msg );
		}
		$result = $this->_controls[ $name ];
		return $result;
	}

	public function load( $data )
	{
		foreach ( $this->_controls as $key => $control )
		{
			if ( array_key_exists( $key, $data ) )
			{
				$this->_controls[ $key ]->setValue( $data[ $key ] );
			}
			if ( $control instanceof Miao_Form_Control_File && array_key_exists(
				$key, $_FILES ) )
			{
				$this->_controls[ $key ]->setValue( $_FILES[ $key ] );
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
			$control->error()->reset();
		}
	}

	/**
	 * @param array $data from data
     * @return bool
	 */
	public function isValid( array $data = array() )
	{
		if ( !empty( $data ) )
		{
			$this->load( $data );
			$result = true;
			foreach ( $this->_controls as $control )
			{
				$result = $control->validate( $this ) && $result;
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
		$pieces[] = $this->renderAttributes();

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
	 * @param string $name
	 * @param array $attributes
	 * @return Miao_Form_Control_Text
	 */
	public function addText( $name, array $attributes = array() )
	{
		$obj = new Miao_Form_Control_Text( $name, $attributes );
		$this->addControl( $obj );
		return $obj;
	}

	/**
	 *
	 * @param string $name
	 * @param array $attributes
	 * @return Miao_Form_Control_Text
	 */
	public function addPassword( $name, array $attributes = array() )
	{
		$obj = new Miao_Form_Control_Password( $name, $attributes );
		$this->addControl( $obj );
		return $obj;
	}

	public function addTextArea( $name, array $attributes = array() )
	{
		$obj = new Miao_Form_Control_Textarea( $name, $attributes );
		$this->addControl( $obj );
		return $obj;
	}

	/**
	 *
	 * @param string $name
	 * @param array $attributes
	 * @return Miao_Form_Control_Submit
	 */
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

	public function addHidden( $name, array $attributes = array() )
	{
		$obj = new Miao_Form_Control_Hidden( $name, $attributes );
		$this->addControl( $obj );
		return $obj;
	}

	public function addSelect( $name, array $attributes = array(), $items = array() )
	{
		$obj = new Miao_Form_Control_Select( $name, $attributes, $items );
		$this->addControl( $obj );
		return $obj;
	}

	public function addCheckbox( $name, array $attributes = array() )
	{
		$obj = new Miao_Form_Control_Checkbox( $name, $attributes );
		$this->addControl( $obj );
		return $obj;
	}

	/**
	 *
	 * @param string $name
	 * @param array $attributes
	 * @param Miao_Form_Control $captcha
	 * @return Miao_Form_Control_Captcha
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

	/**
	 *
	 * @param string $name
	 * @param array $attributes
	 * @return Miao_Form_Control_File
	 */
	public function addFile( $name, array $attributes = array() )
	{
		$obj = new Miao_Form_Control_File( $name, $attributes );
		$this->addControl( $obj );
		return $obj;
	}

	/**
	 * Create Group control, use it when you need multi block controls.
	 *
	 * @example
	 * <code>
	 * <input type="text" name="title[]" />
	 * <input type="text" name="url[]" />
	 * </code>
	 *
	 * @param string $name Group name, may be empty.
	 * @param array $controls
     * @return \Miao_Form_Control_Group
     */
	public function addGroup( $name, array $controls )
	{
		$obj = new Miao_Form_Control_Group( $name );
		foreach ( $controls as $control )
		{
			$obj->addControl( $control );
		}
		$this->addControl( $obj );
		return $obj;
	}

	/**
	 * List control
	 *
	 * @param unknown_type $name
	 * @param unknown_type $control
	 * @return Miao_Form_Control_List
	 */
	public function addList( $control, array $values = array() )
	{
		$obj = new Miao_Form_Control_List( $control, $values );
		$this->addControl( $obj );
		return $obj;
	}

	/**
     * Returns array of errors
     *
     * @return array
     */
	public function getErrors()
	{
		$errors = array();
		/** @var Miao_Form_Control $control */
		/** @var Miao_Form_Validate $validator */
		foreach ( $this->getControls() as $control )
		{
			if ( !$control->isValid() )
			{
				$validator = $control->error();
				$errors[] = array(
					'name' => $control->getName(),
					'error' => $validator->getMessage() );
			}
		}
		return $errors;
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