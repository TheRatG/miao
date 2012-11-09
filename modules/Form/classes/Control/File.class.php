<?php
/**
 * @author vpak
 * @date 2012-09-04 17:34:05
 */
class Miao_Form_Control_File extends Miao_Form_Control_Input
{
	protected $_type = self::TYPE_FILE;

	/**
	 * @param array $value
	 */
	public function setValue( array $value = array() )
	{
		if ( !empty( $value ) )
		{
			if ( !array_key_exists( 'name', $value ) || !array_key_exists(
				'type', $value ) || !array_key_exists( 'tmp_name', $value ) || !array_key_exists(
				'error', $value ) || !array_key_exists( 'size', $value ) )
			{
				throw new Miao_Form_Control_File_Exception( 'Invalid _FILE array' );
			}
		}

		$this->_value = $value;
	}

	/**
	 * @return the $_value
	 */
	public function getValue( $key = '' )
	{
		$result = $this->_value;
		if ( !empty( $result ) && !empty( $key ) && array_key_exists( $key,
			$result ) )
		{
			$result = $result[ $key ];
		}
		return $this->_value;
	}

	/**
	 *
	 * @param string $msg
	 * @return Miao_Form_Control
	 */
	public function setRequired( $msg = '' )
	{
		$val = new Miao_Form_Validate_RequireFile();
		if ( !empty( $msg ) )
		{
			$val->setMessages(
				array( Miao_Form_Validate_Require::IS_EMPTY => $msg ) );
		}
		$this->addValidator( $val );
		return $this;
	}
}