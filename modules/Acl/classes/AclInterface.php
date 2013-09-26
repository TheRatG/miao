<?php
/**
 * User: vpak
 * Date: 26.09.13
 * Time: 10:33
 */

namespace Miao;

interface AclInterface
{
    /**
     * @param string $group
     * @param string $resource
     * @param string $privilege
     * @return bool
     */
    public function isAllowed( $group = null, $resource = null, $privilege = '' );

    public function allow( $group = null, $resource = null, array $privileges = array() );

    public function deny( $group = null, $resource = null, array $privileges = array() );

    public function addResource( $resource );

    public function addGroup( $group );
}