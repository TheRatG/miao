<?php
abstract class Miao_Form_Validate_Base
{

	/**
     * The value to be validated
     *
     * @var mixed
     */
	protected $_value;

	/**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
	protected $_messageVariables = array();

	/**
     * Validation failure message template definitions
     *
     * @var array
     */
	protected $_messageTemplates = array();

	/**
     * Array of validation failure messages
     *
     * @var array
     */
	protected $_messages = array();

	/**
     * Returns array of validation failure messages
     *
     * @return array
     */
	public function getMessages()
	{
		return $this->_messages;
	}

	/**
     * Returns an array of the names of variables that are used in constructing validation failure messages
     *
     * @return array
     */
	public function getMessageVariables()
	{
		return array_keys( $this->_messageVariables );
	}

	/**
     * Returns the message templates from the validator
     *
     * @return array
     */
	public function getMessageTemplates()
	{
		return $this->_messageTemplates;
	}

	/**
	 * Sets the validation failure message template for a particular key
	 *
	 * @param  string $messageString
	 * @param  string $messageKey     OPTIONAL
	 * @return Miao_Form_Validate_Base Provides a fluent interface
	 * @throws Miao_Office_Validate_Exception
	 */
	public function setMessage( $messageString, $messageKey = null )
	{
		if ( $messageKey === null )
		{
			$keys = array_keys( $this->_messageTemplates );
			foreach ( $keys as $key )
			{
				$this->setMessage( $messageString, $key );
			}
			return $this;
		}

		if ( !isset( $this->_messageTemplates[ $messageKey ] ) )
		{
			throw new Miao_Office_Validate_Exception( "No message template exists for key '$messageKey'" );
		}

		$this->_messageTemplates[ $messageKey ] = $messageString;
		return $this;
	}

	/**
	 * Sets validation failure message templates given as an array, where the array keys are the message keys,
	 * and the array values are the message template strings.
	 *
	 * @param  array $messages
	 * @return Miao_Form_Validate_Base
	 */
	public function setMessages( array $messages )
	{
		foreach ( $messages as $key => $message )
		{
			$this->setMessage( $message, $key );
		}
		return $this;
	}

	/**
	 * @param  string $messageKey
	 * @param  string $value      OPTIONAL
	 * @return void
	 */
	protected function _error( $messageKey, $value = null )
	{
		if ( $messageKey === null )
		{
			$keys = array_keys( $this->_messageTemplates );
			$messageKey = current( $keys );
		}
		if ( $value === null )
		{
			$value = $this->_value;
		}
		$this->_messages[ $messageKey ] = $this->_createMessage( $messageKey, $value );
	}

	/**
	 * Constructs and returns a validation failure message with the given message key and value.
	 *
	 * Returns null if and only if $messageKey does not correspond to an existing template.
	 *
	 * If a translator is available and a translation exists for $messageKey,
	 * the translation will be used.
	 *
	 * @param  string $messageKey
	 * @param  string $value
	 * @return string
	 */
	protected function _createMessage( $messageKey, $value )
	{
		if ( !isset( $this->_messageTemplates[ $messageKey ] ) )
		{
			return null;
		}
		$message = $this->_messageTemplates[ $messageKey ];
		$message = str_replace( '%value%', ( string ) $value, $message );
		foreach ( $this->_messageVariables as $ident => $property )
		{
			$message = str_replace( "%$ident%", ( string ) $this->$property, $message );
		}
		return $message;
	}

	/**
	 * Sets the value to be validated and clears the messages and errors arrays
	 *
	 * @param  mixed $value
	 * @return void
	 */
	protected function _setValue( $value )
	{
		$this->_value = $value;
		$this->_messages = array();
	}

	abstract public function isValid( $value );
}