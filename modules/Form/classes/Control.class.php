<?php
abstract class Miao_Form_Control
{

	/**
	 *
	 * @var string
	 */
	protected $_id;

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

	protected $_exceptAttrMap = array( 'id', 'name', 'value' );

	/**
	 *
	 * @param string $id
	 * @param array $attributes
	 */
	public function __construct( $id, array $attributes = array() )
	{
		$this->setId( $id );
		$this->setAttributes( $attributes );
		$this->_label = new Miao_Form_Label( '' );
	}

	public function __get( $propertyName )
	{
		$result = null;
		if ( 'label' == $propertyName )
		{
			$result = $this->_label->getLabel();
		}

		if ( is_null( $result ) )
		{
			$msg = sprintf( 'Call undefined property (%s)', $propertyName );
			throw new Miao_Form_Exception( $msg );
		}

		return $result;
	}

	public function __toString()
	{
		return $this->render();
	}

	/**
	 * @return the $_id
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * @return the $_value
	 */
	public function getValue()
	{
		return $this->_value;
	}

	/**
	 * @param field_type $value
	 */
	public function setValue( $value )
	{
		$this->_value = $value;
	}

	/**
	 * @param string $id
	 */
	public function setId( $id )
	{
		$this->_id = $id;
	}

	/**
	 * @return the $_id
	 */
	public function getName()
	{
		return $this->_id;
	}

	/**
	 * @param string $name
	 */
	public function setName( $name )
	{
		$this->_id = $name;
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

	public function setLabel( $label )
	{
		$this->_label->setLabel( $label );
	}

	public function getLabel()
	{
		return $this->_label;
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