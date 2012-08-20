<?php
class Miao_Acl_Adapter_Default implements Miao_Acl_Adapter_Interface
{
	const ALLOW = 'allow';
	const DENY = 'deny';

	protected $_resource = array();

	protected $_group = array();

	protected $_allow;

	protected $_deny;

	public function isAllowed( $group = null, $resource = null, $privilege = '' )
	{
		$result = null;
		if ( is_null( $group ) )
		{
			$group = '*';
		}
		else
		{
			$result = $this->_checkGroup( $group );
		}
		if ( is_null( $resource ) )
		{
			$resource = '*';
		}
		else
		{
			$result = $this->_checkResource( $resource );
		}

		if ( false !== $result )
		{
			$permission = $this->_deny;
			$result = $this->_isDeny( $permission, $group, $resource, $privilege );
			if ( true === $result )
			{
				$result = false;
			}
			else
			{
				$permission = $this->_allow;
				$result = $this->_isAllow( $permission, $group, $resource, $privilege );
			}
		}
		return $result;
	}

	public function allow( $group = null, $resource = null, array $privileges = array() )
	{
		$this->_changePermisssion( self::ALLOW, $group, $resource, $privileges );
		return $this;
	}

	public function deny( $group = null, $resource = null, array $privileges = array() )
	{
		$this->_changePermisssion( self::DENY, $group, $resource, $privileges );
		return $this;
	}

	public function addResource( $resource )
	{
		$this->_resource[ $resource ] = '';
		return $this;
	}

	public function addGroup( $group )
	{
		$this->_group[ $group ] = '';
		return $this;
	}

	protected function _changePermisssion( $type, $group, $resource, $privileges = null )
	{
		if ( is_null( $group ) )
		{
			$group = '*';
		}
		else
		{
			$this->_checkGroup( $group, true );
		}
		if ( is_null( $resource ) )
		{
			$resource = '*';
		}
		else
		{
			$this->_checkResource( $resource, true );
		}

		$permission = &$this->_deny;
		if ( $type == self::ALLOW )
		{
			$permission = &$this->_allow;
		}

		$permission[ $group ][ $resource ] = $privileges;
	}

	protected function _isAllow( &$permission, $group, $resource, $privilege )
	{
		$result = false;
		if ( isset( $permission[ $group ] ) )
		{
			$resourceList = $permission[ $group ];
			foreach ( $resourceList as $sResource => $sPrivilege )
			{
				if ( '*' == $sResource )
				{
					$result = true;
					break;
				}

				if ( $resource == $sResource )
				{
					$result = true;
					if ( $privilege && !empty( $sPrivilege ) )
					{
						$result = in_array( $privilege, $sPrivilege );
					}
				}
			}
		}
		return $result;
	}

	protected function _isDeny( &$permission, $group, $resource, $privilege )
	{
		$resourceList = array();
		if ( isset( $permission[ '*' ] ) )
		{
			$resourceList = $permission[ '*' ];
		}
		else if ( isset( $permission[ $group ] ) )
		{
			$resourceList = $permission[ $group ];
		}
		$result = $this->_isDenyByResourceList( $resourceList, $resource, $privilege );
		return $result;
	}

	protected function _isDenyByResourceList( array $resourceList, $resource, $privilege )
	{
		$result = false;
		foreach ( $resourceList as $sResource => $sPrivilege )
		{
			if ( '*' == $sResource )
			{
				$result = true;
				break;
			}

			if ( $resource == $sResource )
			{
				if ( empty( $sPrivilege ) )
				{
					$result = true;
				}
				else if ( $privilege && !empty( $sPrivilege ) )
				{
					$result = in_array( $privilege, $sPrivilege );
				}
			}
		}
		return $result;
	}

	protected function _checkGroup( $group, $throwException = false )
	{
		$result = false;
		foreach ( $this->_group as $sGroup => $value )
		{
			if ( $sGroup == $group )
			{
				$result = true;
				break;
			}
		}
		if ( false === $result && $throwException )
		{
			$message = sprintf( 'Group (%s) did not define', $group );
			throw new Miao_Acl_Exception( $message );
		}
		return $result;
	}

	protected function _checkResource( $resource, $throwException = false )
	{
		$result = false;
		foreach ( $this->_resource as $sResource => $value )
		{
			if ( $sResource == $resource )
			{
				$result = true;
				break;
			}
		}
		if ( false === $result && $throwException )
		{
			$message = sprintf( 'Resource (%s) did not define', $resource );
			throw new Miao_Acl_Exception( $message );
		}
		return $result;
	}
}