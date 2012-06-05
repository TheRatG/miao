<?php
/**
 *
 */
abstract class Miao_Form_Control
{

	/**
	 *
	 * @var string
	 */
	protected $_name;

	/**
	 *
	 * @var string
	 */
	protected $_value;

	/**
	 *
	 * @var array
	 */
	protected $_attributes = array();

	/**
	 *
	 * @var Miao_Form_Validate
	 */
	protected $_validator;

	/**
	 *
	 * @var Miao_Form_Label
	 */
	protected $_label;

	/**
	 * Disallow attributes
	 * @var array
	 */
	protected $_exceptAttrMap = array( 'name', 'value' );

	/**
	 *
	 * @param string $id
	 * @param array $attributes
	 */
	public function __construct( $name, array $attributes = array() )
	{
		$this->setName( $name );
		$this->setAttributes( $attributes );
		$this->_label = new Miao_Form_Label( '' );
		$this->_validator = new Miao_Form_Validate();
	}

	public function __toString()
	{
		return $this->render();
	}

	/**
	 * @return the $_value
	 */
	public function getValue()
	{
		return $this->_value;
	}

	/**
	 * @param string $value
	 */
	public function setValue( $value )
	{
		$this->_value = $value;
	}

	/**
	 * @return the $_name
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @param string $name
	 */
	public function setName( $name )
	{
		$this->_name = $name;
	}

	/**
	 * @return the $_attributes
	 */
	public function getAttributes()
	{
		return $this->_attributes;
	}

	/**
	 * @param array $attributes
	 */
	public function setAttributes( array $attributes )
	{
		foreach ( $attributes as $name => $value )
		{
			$this->addAttribute( $name, $value );
		}
	}

	/**
	 *
	 * @param string $name
	 * @param string $value
	 * @throws Miao_Form_Exception
	 */
	public function addAttribute( $name, $value )
	{
		if ( in_array( $name, $this->_exceptAttrMap ) )
		{
			$msg = sprintf( 'Invalid attr name (%s), you should use method (set%s)', $name, ucfirst( $name ) );
			throw new Miao_Form_Exception( $msg );
		}
		$this->_attributes[ $name ] = $value;
	}

	/**
	 *
	 * @return Miao_Form_Label
	 */
	public function label()
	{
		return $this->_label;
	}

	public function error()
	{
		return $this->_validator;
	}

	/**
	 *
	 * @param string $label
	 * @return Miao_Form_Control
	 */
	public function setLabel( $label )
	{
		$this->_label->setLabel( $label );
		return $this;
	}

	/**
	 *
	 * @param mixed $validator
	 * @param bool $breakChainOnFailure
	 * @return Miao_Form_Control
	 */
	public function addValidator( $validator, $breakChainOnFailure = false )
	{
		$this->_validator->addValidator( $validator, $breakChainOnFailure );
		return $this;
	}

	/**
	 *
	 * @param unknown_type $msg
	 * @return Miao_Form_Control
	 */
	public function setRequired( $msg = '' )
	{
		$val = new Miao_Form_Validate_Require();
		if ( !empty( $msg ) )
		{
			$val->setMessages( array(
				Miao_Form_Validate_Require::IS_EMPTY => $msg ) );
		}
		$this->addValidator( $val );
		return $this;
	}

	/**
	 *
	 * @return boolean
	 */
	public function isValid()
	{
		$value = $this->getValue();
		$result = $this->_validator->isValid( $value );
		return $result;
	}

	abstract public function render();

	protected function _renderAttributes()
	{
		$pieces = array();
		foreach ( $this->getAttributes() as $name => $value )
		{
			$pieces[] = sprintf( '%s="%s"', $name, $value );
		}
		$result = implode( ' ', $pieces );
		return $result;
	}
}