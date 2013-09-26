<?php
/**
 * @author vpak
 * @date 2013-09-26 11:22:00
 */

namespace Miao\Auth\Storage;

interface StorageInterface
{
    /**
     * @return bool
     */
    public function isEmpty();

    public function read();

    public function write( $contents );

    public function clear();
}