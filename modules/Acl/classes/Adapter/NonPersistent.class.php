<?php
class Miao_Acl_Adapter_NonPersistent implements Miao_Acl_Adapter_Interface
{

	private $_group = array();

	private $_resource = array();

	private $_permission = array();

	/**
	 *
	 * @param string $group
	 * @param string $resourceName
	 * @param string $privilege
	 * @return bool
	 */
	public function isAllowed( $group, $resource, $privilege = null )
	{
		$result = false;

		$groupId = $this->getGroupId( $group );
		$resourceId = $this->getResourceId( $resource );

		if ( isset( $this->_permission[ $groupId ] ) )
		{
			$pRes = $this->_permission[ $groupId ];
			if ( isset( $pRes[ '*' ] ) )
			{
				$result = true;
			}
			else if ( isset( $pRes[ $resourceId ] ) )
			{
				$permission = $pRes[ $resourceId ];
				if ( isset( $permission[ 'allow' ] ) )
				{
					$privileges = $permission[ 'allow' ];
					if ( !is_null( $privilege ) && !empty( $privileges ) )
					{
						if ( false !== array_search( $privilege, $privileges ) )
						{
							$result = true;
						}
					}
					else
					{
						$result = true;
					}
				}
			}
		}

		return $result;
	}

	/**
	 *
	 * @param string $group
	 */
	public function addGroup( $group )
	{
		assert( !empty( $group ) );
		assert( is_string( $group ) );

		$id = $this->_getGroupId( $group );
		if ( false !== $id )
		{
			$message = sprintf( 'Group (%s) already exists', $group );
			throw new Miao_Acl_Adapter_Exception( $message );
		}

		$this->_group[] = $group;
		return $this;
	}

	public function deleteGroup( $group )
	{
		$id = $this->getGroupId( $group );
		if ( false !== $id )
		{
			unset( $this->_group[ $id ] );
		}
		return $this;
	}

	public function add( $resource )
	{
		assert( !empty( $resource ) );
		assert( is_string( $resource ) );

		$id = $this->_getResourceId( $resource );
		if ( false !== $id )
		{
			$message = sprintf( 'Resource (%s) already exists', $resource );
			throw new Miao_Acl_Adapter_Exception( $message );
		}
		$this->_resource[] = $resource;
		return $this;
	}

	public function delete( $resource )
	{
		$id = $this->getResourceId( $resource );
		if ( false !== $id )
		{
			unset( $this->_resource[ $id ] );
		}
		return $this;
	}

	public function allow( $group = null, $resource = null, array $privileges = array() )
	{
		$groupId = $this->_getGroupId( $group, true );
		$resourceId = '*';
		if ( '*' !== $resource )
		{
			$resourceId = $this->_getResourceId( $resource, true );
		}
		if ( !isset( $this->_permission[ $groupId ] ) )
		{
			$this->_permission[ $groupId ] = array();
		}
		$this->_permission[ $groupId ][ $resourceId ][ 'allow' ] = $privileges;
	}

	public function deny( $group = null, $resource = null, $privilege = null )
	{
		$groupId = $this->_getGroupId( $group, true );
		$resourceId = $this->_getResourceId( $resource, true );
		if ( isset( $this->_permission[ $groupId ] ) )
		{
			$this->_permission[ $groupId ] = array();
		}
		$this->_permission[ $groupId ][ $resourceId ][ 'deny' ] = true;
	}

	protected function _getGroupId( $group, $throwException = false )
	{
		$key = array_search( $group, $this->_group );

		if ( $throwException && false === $key )
		{
			$message = sprintf( 'Group (%s) is not defined', $group );
			throw new Miao_Acl_Adapter_Exception( $message );
		}

		return $key;
	}

	protected function _getResourceId( $resource, $throwException = false )
	{
		$key = array_search( $resource, $this->_resource );

		if ( $throwException && false === $key )
		{
			$message = sprintf( 'Resource (%s) is not defined', $resource );
			throw new Miao_Acl_Adapter_Exception( $message );
		}
		return $key;
	}
}