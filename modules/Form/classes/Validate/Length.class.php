<?php
class Miao_Form_Validate_Length extends Miao_Form_Validate_Base
{
	const INVALID = 'lengthInvalid';
	const TOO_SHORT = 'lengthTooShort';
	const TOO_LONG = 'lengthTooLong';

	/**
     * @var array
     */
	protected $_messageTemplates = array(
		self::INVALID => "Invalid type given. String expected",
		self::TOO_SHORT => "'%value%' is less than %min% characters long",
		self::TOO_LONG => "'%value%' is more than %max% characters long" );

	/**
     * @var array
    */
	protected $_messageVariables = array( 'min' => '_min', 'max' => '_max' );

	/**
     * Minimum length
     *
     * @var integer
     */
	protected $_min;

	/**
     * Maximum length
     *
     * If null, there is no maximum length
     *
     * @var integer|null
     */
	protected $_max;

	/**
	 * Sets validator options
	 *
	 * @param  integer|array|Zend_Config $options
	 * @return void
	 */
	public function __construct( $max, $min = 0 )
	{
		$this->setMin( $min );
		$this->setMax( $max );
	}

	/**
     * Returns the min option
     *
     * @return integer
     */
	public function getMin()
	{
		return $this->_min;
	}

	/**
     * Sets the min option
     *
     * @param  integer $min
     * @throws Miao_Form_Validate_Exception
     * @return Miao_Validate_Length Provides a fluent interface
     */
	public function setMin( $min )
	{
		if ( null !== $this->_max && $min > $this->_max )
		{
			throw new Miao_Form_Validate_Exception( "The minimum must be less than or equal to the maximum length, but $min >" . " $this->_max" );
		}
		$this->_min = max( 0, ( integer ) $min );
		return $this;
	}

	/**
    	* Returns the max option
    		*
    		* @return integer|null
    		*/
	public function getMax()
	{
		return $this->_max;
	}

	/**
	 *
	 * Sets the max option
	 *
	 * @param  integer|null $max
	 * @throws Miao_Form_Validate_Exception
	 * @return Miao_Validate_Length Provides a fluent interface
	 */
	public function setMax( $max )
	{
		if ( null === $max )
		{
			$this->_max = null;
		}
		else if ( $max < $this->_min )
		{
			throw new Miao_Form_Validate_Exception( "The maximum must be greater than or equal to the minimum length, but " . "$max < $this->_min" );
		}
		else
		{
			$this->_max = ( integer ) $max;
		}

		return $this;
	}

	/**
	  * Returns true if and only if the string length of $value is at least the min option and
	  * no greater than the max option (when the max option is not null).
	  *
	  * @param  string $value
	  * @return boolean
	  */
	public function isValid( $value )
	{
		if ( !is_string( $value ) )
		{
			$this->_error( self::INVALID );
			return false;
		}

		$this->_setValue( $value );
		$length = iconv_strlen( $value );

		if ( $length < $this->_min )
		{
			$this->_error( self::TOO_SHORT );
		}

		if ( null !== $this->_max && $this->_max < $length )
		{
			$this->_error( self::TOO_LONG );
		}

		if ( count( $this->getMessages() ) )
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}