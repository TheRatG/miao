<?php
/**
 * @author vpak
 * @date 2012-08-15 10:29:49
 */
interface Miao_Auth_Adapter_Interface
{
	/**
	 *
	 * @param scalar $login
	 * @param scalar $password
	 * @param array $options
	 * @return Miao_Auth_Result
	 */
	public function login( $login, $password, array $options = array() );

	/**
	 *
	 * @param scalar $login
	 * @return bool
	 */
	public function logout( $login );
}