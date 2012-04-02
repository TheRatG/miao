<?php
/**
* UniPg
* @package Office
*/

/**
 * Class for Miao_Office_Request-exceptions.
 *
 * @author S.Vyazovetskov
 * @copyright RBC 2006
 * @package Office
 */

class Miao_Office_Request_Exception_OnVarNotExists extends Miao_Office_Request_Exception
{
	/**
	 * @param string $source
	 * @param string $varName
	 */
	public function __construct( $source, $varName )
	{
		$message = 'Request variable with name "' . $varName . '" was not recieved in "' . $source . '"';
		parent::__construct( $message );
	}
}
