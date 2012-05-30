<?php
class Miao_Session_Exception_OnVariableNotExists extends Miao_Session_Exception
{
	/**
	 * @param string $varName имя переменной
	 */
	public function __construct( $varName )
	{
		parent::__construct( 'Variable with name "' .$varName. '" was not stored in session' );
	}
}
