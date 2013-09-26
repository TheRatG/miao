<?php
/**
 * @author vpak
 * @date 2013-09-26 10:14:09
 */

namespace Miao;

class Acl
{
    /**
     * @var \Miao\Acl\Adapter\AdapterInterface
     */
    protected $_adapter;

    /**
     * @param \Miao\Acl\Adapter\AdapterInterface $adapter
     */
    public function __construct( \Miao\Acl\Adapter\AdapterInterface $adapter )
    {
        $this->_adapter = $adapter;
    }

    /**
     * @param string $group
     * @param $resource
     * @param string $privilege
     * @return bool
     */
    public function isAllowed( $group, $resource, $privilege = null )
    {
        return $this->_adapter->isAllowed( $group, $resource, $privilege );
    }

    /**
     * @param string $group
     * @return \Miao\Acl\Adapter\AdapterInterface
     */
    public function addGroup( $group )
    {
        return $this->_adapter->addGroup( $group );
    }

    /**
     * @param string $group
     * @return \Miao\Acl\Adapter\AdapterInterface
     */
    public function deleteGroup( $group )
    {
        return $this->_adapter->deleteGroup( $group );
    }

    /**
     * @param $resource
     * @return \Miao\Acl\Adapter\AdapterInterface
     */
    public function addResource( $resource )
    {
        return $this->_adapter->addResource( $resource );
    }

    /**
     * @param string $resource
     * @return \Miao\Acl\Adapter\AdapterInterface
     */
    public function delete( $resource )
    {
        return $this->_adapter->delete( $resource );
    }

    /**
     * @param string $group
     * @param string $resource
     * @param array $privileges
     * @return \Miao\Acl\Adapter\AdapterInterface
     */
    public function allow( $group = null, $resource = null, array $privileges = array() )
    {
        return $this->_adapter->allow( $group, $resource, $privileges );
    }

    /**
     * @param string $group
     * @param string $resource
     * @param array $privileges
     * @return \Miao\Acl\Adapter\AdapterInterface
     */
    public function deny( $group = null, $resource = null, array $privileges = array() )
    {
        return $this->_adapter->deny( $group, $resource, $privileges );
    }
}