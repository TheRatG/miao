<?php
/**
 * @author vpak
 * @date 2012-10-18 10:38:51
 */
class Miao_Router_Rule_Validator_Regexp extends Miao_Router_Rule_Validator
{
	private $_pattern;

	public function __construct( array $config )
	{
		if ( !isset( $config[ 'pattern' ] ) )
		{
			throw new Miao_Router_Rule_Validator_Exception( 'Invalid config: param "pattern" was not found' );
		}
		$this->_pattern = $config[ 'pattern' ];
	}

	public function test( $value )
	{
		$result = preg_match( $this->_pattern, $value );
		return $result;
	}
}
