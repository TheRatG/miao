<?php
interface Miao_Acl_Adapter_Interface
{
	/**
	 *
	 * @param string $group
	 * @param string $resource
	 * @param string $privilege
	 */
	public function isAllowed( $group = null, $resource = null, $privilege = '' );

	public function allow( $group = null, $resource = null, array $privileges = array() );

	public function deny( $group = null, $resource = null, array $privileges = array() );

	public function addResource( $resource );

	public function addGroup( $group );
}