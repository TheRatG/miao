<?php
/**
 * @author vpak
 * @date 2013-09-26 11:22:22
 */

namespace Miao\Auth\Adapter;

interface AdapterInterface
{
    /**
     * @param scalar $login
     * @param scalar $password
     * @param array $options
     * @return \Miao\Auth\Result
     */
    public function login( $login, $password, array $options = array() );

    /**
     * @param $result
     * @return bool
     */
    public function logout( \Miao\Auth\Result $result );

    /**
     * @param \Miao\Auth\Result $result
     * @return bool
     */
    public function check( \Miao\Auth\Result $result );

    /**
     * Use this method for options remember me
     * @return \Miao\Auth\Result
     */
    public function restore();
}