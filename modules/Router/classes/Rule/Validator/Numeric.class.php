<?php
/**
 * @author vpak
 * @date 2012-10-18 10:39:03
 */
class Miao_Router_Rule_Validator_Numeric extends Miao_Router_Rule_Validator
{
	public function test( $value )
	{
		$value = ( string ) trim( $value );
		$result = is_numeric( $value );
		return $result;
	}
}