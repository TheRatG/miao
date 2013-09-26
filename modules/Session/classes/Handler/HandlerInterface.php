<?php
/**
 * User: vpak
 * Date: 26.09.13
 * Time: 17:18
 */

namespace Miao\Session\Handler;

interface HandlerInterface
{
    /**
     * Open Session - retrieve resources
     * @param string $savePath
     * @param string $name
     */
    public function open( $savePath, $name );

    /**
     * Close Session - free resources

     */
    public function close();

    /**
     * Read session data
     * @param string $id
     */
    public function read( $id );

    /**
     * Write Session - commit data to resource
     * @param string $id
     * @param mixed $data
     */
    public function write( $id, $data );

    /**
     * Destroy Session - remove data from resource for
     * given session id
     * @param string $id
     */
    public function destroy( $id );

    /**
     * Garbage Collection - remove old session data older
     * than $maxLifetime (in seconds)
     * @param int $maxLifetime
     */
    public function gc( $maxLifetime );
}