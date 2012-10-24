<?php
/**
 * System validator
 * @author vpak
 * @date 2012-10-17 18:29:44
 */
class Miao_Router_Rule_Validator_Compare extends Miao_Router_Rule_Validator
{
	private $_str;

	public function __construct( array $config )
	{
		if ( !isset( $config[ 'str' ] ) )
		{
			throw new Miao_Router_Rule_Validator_Exception( 'Invalid config: param "str" was not found' );
		}
		$this->_str = $config[ 'str' ];
	}

	public function test( $value )
	{
		$result = false;
		if ( 0 === strcmp( $value, $this->_str ) )
		{
			$result = true;
		}
		return $result;
	}
    
    public function getPattern()
    {
        return $this->_str;
    }
}
