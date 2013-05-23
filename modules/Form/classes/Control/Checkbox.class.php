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
	protected $_useHiddenElement = true;

	/**
	 * @var string
	 */
	protected $_uncheckedValue = '0';

	/**
	 * @var string
	 */
	protected $_checkedValue = '1';

	/**
	 * Do we render hidden element?
	 *
	 * @param  bool $useHiddenElement
	 * @return Checkbox
	 */
	public function setUseHiddenElement( $useHiddenElement )
	{
		$this->_useHiddenElement = ( bool ) $useHiddenElement;
		return $this;
	}

	/**
	 * Do we render hidden element?
	 *
	 * @return bool
	 */
	public function useHiddenElement()
	{
		return $this->_useHiddenElement;
	}

	/**
	 * Set the value to use when checkbox is unchecked
	 *
	 * @param $uncheckedValue
	 * @return Checkbox
	 */
	public function setUncheckedValue( $uncheckedValue )
	{
		$this->_uncheckedValue = $uncheckedValue;
		return $this;
	}

	/**
	 * Get the value to use when checkbox is unchecked
	 *
	 * @return string
	 */
	public function getUncheckedValue()
	{
		return $this->_uncheckedValue;
	}

	/**
	 * Set the value to use when checkbox is checked
	 *
	 * @param $checkedValue
	 * @return Checkbox
	 */
	public function setCheckedValue( $checkedValue )
	{
		$this->_checkedValue = $checkedValue;
		return $this;
	}

	/**
	 * Get the value to use when checkbox is checked
	 *
	 * @return string
	 */
	public function getCheckedValue()
	{
		return $this->_checkedValue;
	}

	/**
	 * Checks if this checkbox is checked.
	 *
	 * @return bool
	 */
	public function isChecked()
	{
		return ( bool ) $this->_value;
	}

	/**
	 * Checks or unchecks the checkbox.
	 *
	 * @param bool $value The flag to set.
	 * @return Checkbox
	 */
	public function setChecked( $value )
	{
		$this->_value = ( bool ) $value;
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
			$this->_value = $value;
		}
		else
		{
			$this->_value = $value === $this->getCheckedValue();
		}
		return $this;
	}

	/**
	 * @return the $_value
	 */
	public function getValue()
	{
		$result = $this->getUncheckedValue();
		if ( $this->isChecked() )
		{
			$result = $this->getCheckedValue();
		}
		return $result;
	}

	public function render()
	{
		$pieces = array();

		if ( $this->isChecked() )
		{
			$this->addAttribute( 'checked', 'checked' );
		}

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
		$pieces[] = $this->renderAttributes();
		$pieces[] = '/>';

		$result = trim( implode( ' ', $pieces ) );
		return $result;
	}
}