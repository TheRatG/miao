<?php
/**
 * @author vpak
 * @date 2012-11-08 18:33:49
 */
class Miao_Form_Control_Checkbox extends Miao_Form_Control_Input
{
	protected $_type = self::TYPE_CHECKBOX;

	/**
	 * @var bool
	 */
	protected $useHiddenElement = true;

	/**
	 * @var string
	 */
	protected $uncheckedValue = '0';

	/**
	 * @var string
	 */
	protected $checkedValue = '1';

	/**
	 * Do we render hidden element?
	 *
	 * @param  bool $useHiddenElement
	 * @return Checkbox
	 */
	public function setUseHiddenElement( $useHiddenElement )
	{
		$this->useHiddenElement = ( bool ) $useHiddenElement;
		return $this;
	}

	/**
	 * Do we render hidden element?
	 *
	 * @return bool
	 */
	public function useHiddenElement()
	{
		return $this->useHiddenElement;
	}

	/**
	 * Set the value to use when checkbox is unchecked
	 *
	 * @param $uncheckedValue
	 * @return Checkbox
	 */
	public function setUncheckedValue( $uncheckedValue )
	{
		$this->uncheckedValue = $uncheckedValue;
		return $this;
	}

	/**
	 * Get the value to use when checkbox is unchecked
	 *
	 * @return string
	 */
	public function getUncheckedValue()
	{
		return $this->uncheckedValue;
	}

	/**
	 * Set the value to use when checkbox is checked
	 *
	 * @param $checkedValue
	 * @return Checkbox
	 */
	public function setCheckedValue( $checkedValue )
	{
		$this->checkedValue = $checkedValue;
		return $this;
	}

	/**
	 * Get the value to use when checkbox is checked
	 *
	 * @return string
	 */
	public function getCheckedValue()
	{
		return $this->checkedValue;
	}

	/**
	 * Checks if this checkbox is checked.
	 *
	 * @return bool
	 */
	public function isChecked()
	{
		return ( bool ) $this->value;
	}

	/**
	 * Checks or unchecks the checkbox.
	 *
	 * @param bool $value The flag to set.
	 * @return Checkbox
	 */
	public function setChecked( $value )
	{
		$this->value = ( bool ) $value;
		return $this;
	}

	/**
	 * Checks or unchecks the checkbox.
	 *
	 * @param mixed $value A boolean flag or string that is checked against the "checked value".
	 * @return Element
	 */
	public function setValue( $value )
	{
		if ( is_bool( $value ) )
		{
			$this->value = $value;
		}
		else
		{
			$this->value = $value === $this->getCheckedValue();
		}
		return $this;
	}

	public function render()
	{
		$pieces = array();

		if ( $this->useHiddenElement() )
		{
			$pieces[] = '<input type="hidden"';
			$pieces[] = sprintf( 'name="%s"', htmlspecialchars( $this->getName() ) );
			$pieces[] = 'value="0"';
			$pieces[] = '/>';
		}

		$pieces[] = '<input';
		$pieces[] = sprintf( 'name="%s"', htmlspecialchars( $this->getName() ) );
		$value = $this->getValue();
		if ( is_scalar( $value ) )
		{
			$pieces[] = sprintf( 'value="%s"', htmlspecialchars( $value ) );
		}
		else
		{
			$pieces[] = sprintf( 'value="1"' );
		}
		$pieces[] = $this->_renderType();
		$pieces[] = $this->_renderAttributes();
		$result = trim( implode( ' ', $pieces ) ) . ' />';
		return $result;
	}
}