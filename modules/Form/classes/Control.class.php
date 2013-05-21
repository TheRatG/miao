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

	protected $_isValid = true;

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
		$this->_label = new Miao_Form_Label( $name, '' );
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
		return $this;
	}

	/**
	 * @return string the $_name
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @param string $name
     * @return Miao_Form_Control
	 */
	public function setName( $name )
	{
		$this->_name = $name;
		return $this;
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
		return $this;
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
		return $this;
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
	public function setLabel( $label, array $attributes = array() )
	{
		$this->_label->setLabel( $label, $attributes );
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
	 * @param string $msg
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

	public function isRequired()
	{
		$result = false;
		foreach( $this->_validator->getValidators() as $validator )
		{
			if ( isset( $validator[ 'instance' ] ) && $validator[ 'instance' ] instanceof Miao_Form_Validate_Require )
			{
				$result = true;
				break;
			}
		}
		return $result;
	}

	/**
	 *
	 * @return boolean
	 */
	public function isValid()
	{
		$result = $this->_isValid;
		return $result;
	}

	public function validate( Miao_Form $form = null )
	{
		$this->_isValid = $this->_validator->isValid( $this->getValue(), $form );
		return $this->_isValid;
	}

	public function clearValue()
	{
		$this->_value = '';
	}

	abstract public function render();

	public function renderAttributes()
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