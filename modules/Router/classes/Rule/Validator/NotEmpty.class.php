<?php
/**
 * @author vpak
 * @date 2012-10-18 10:39:08
 */
class Miao_Router_Rule_Validator_NotEmpty extends Miao_Router_Rule_Validator
{

	public function test( $value )
	{
		$value = ( string ) trim( $value );
		$result = ( '' !== $value );
		return $result;
	}
    
    public function getPattern()
    {
        return '[^/]+';
    }
}
