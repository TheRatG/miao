<?php
class Miao_Acl
{

	/**
	 *
	 * @var Miao_Acl_Adapter_Interface
	 */
	protected $_adapter;

	/**
	 *
	 * @param Miao_Acl_Adapter_Interface $adapter
	 */
	public function __construct( Miao_Acl_Adapter_Interface $adapter )
	{
		$this->_adapter = $adapter;
	}

	/**
	 *
	 * @param string $group
	 * @param string $resourceName
	 * @param string $privilege
	 */
	public function isAllowed( $group, $resource, $privilege = null )
	{
		return $this->_adapter->isAllowed( $group, $resource, $privilege );
	}

	/**
	 *
	 * @param string $group
	 */
	public function addGroup( $group )
	{
		return $this->_adapter->addGroup( $group );
	}

	/**
	 *
	 * @param string $group
	 */
	public function deleteGroup( $group )
	{
		return $this->_adapter->deleteGroup( $group );
	}

	/**
	 *
	 * @param string $resource
	 */
	public function addResource( $resource )
	{
		return $this->_adapter->addResource( $resource );
	}

	/**
	 *
	 * @param string $resource
	 */
	public function delete( $resource )
	{
		return $this->_adapter->delete( $resource );
	}

	/**
	 *
	 * @param string $group
	 * @param string $resource
	 * @param array $privileges
	 */
	public function allow( $group = null, $resource = null, array $privileges = array() )
	{
		return $this->_adapter->allow( $group, $resource, $privileges );
	}

	/**
	 *
	 * @param string $group
	 * @param string $resource
	 */
	public function deny( $group = null, $resource = null, array $privileges = array() )
	{
		return $this->_adapter->deny( $group, $resource, $privileges );
	}
}