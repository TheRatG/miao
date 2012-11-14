<?php
/**
 * @author vpak
 * @date 2012-10-18 10:39:08
 */
class Miao_Router_Rule_Validator_NotEmpty extends Miao_Router_Rule_Validator
{
	private $_min = 1;
	private $_max = null;

	public function __construct( array $config )
	{
		if ( array_key_exists( 'min', $config ) )
		{
			$this->_min = $config[ 'min' ];
		}
		if ( array_key_exists( 'max', $config ) )
		{
			$this->_max = $config[ 'max' ];
		}
	}

	public function test( $value )
	{
		$value = ( string ) trim( $value );
		$result = ( '' !== $value );
		$len = strlen( $value );
		if ( $this->_min )
		{
			if ( $len < $this->_min )
			{
				$result = false;
			}
		}
		if ( $result && $this->_max )
		{
			if ( $len > $this->_max )
			{
				$result = false;
			}
		}

		return $result;
	}

    public function getPattern()
    {
        return '[^/]+';
    }
}
