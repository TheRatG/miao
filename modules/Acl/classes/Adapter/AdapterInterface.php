<?php
/**
 * User: vpak
 * Date: 26.09.13
 * Time: 10:33
 */

namespace Miao\Acl\Adapter;

interface AdapterInterface
{
    /**
     * @param string $group
     * @param string $resource
     * @param string $privilege
     * @return bool
     */
    public function isAllowed( $group = null, $resource = null, $privilege = '' );

    /**
     * @param null $group
     * @param null $resource
     * @param array $privileges
     * @return \Miao\Acl\Adapter\AdapterInterface
     */
    public function allow( $group = null, $resource = null, array $privileges = array() );

    /**
     * @param null $group
     * @param null $resource
     * @param array $privileges
     * @return \Miao\Acl\Adapter\AdapterInterface
     */
    public function deny( $group = null, $resource = null, array $privileges = array() );

    /**
     * @param $resource
     * @return \Miao\Acl\Adapter\AdapterInterface
     */
    public function addResource( $resource );

    /**
     * @param $group
     * @return \Miao\Acl\Adapter\AdapterInterface
     */
    public function addGroup( $group );

    /**
     * @param string $group
     * @return \Miao\Acl\Adapter\AdapterInterface
     */
    public function deleteGroup( $group );

    /**
     * @param string $resource
     * @return \Miao\Acl\Adapter\AdapterInterface
     */
    public function delete( $resource );
}