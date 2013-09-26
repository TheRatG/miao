<?php
/**
 *
 * @author vpak
 * @date 2013-09-26 11:22:16
 */

namespace Miao\Auth\Adapter;

class Standart implements \Miao\Auth\Adapter\AdapterInterface
{
    protected $_data;

    public function __construct( array $data )
    {
        $this->_data;
    }

    /**
     * @param scalar $login
     * @param scalar $password
     * @param array $options
     * @return \Miao\Auth\Result
     */
    public function login( $login, $password, array $options = array() )
    {
        // TODO: Implement login() method.
    }

    /**
     * @param $result
     * @return bool
     */
    public function logout( \Miao\Auth\Result $result )
    {
        // TODO: Implement logout() method.
    }

    /**
     * @param \Miao\Auth\Result $result
     * @return bool
     */
    public function check( \Miao\Auth\Result $result )
    {
        // TODO: Implement check() method.
    }

    /**
     * Use this method for options remember me
     * @return \Miao\Auth\Result
     */
    public function restore()
    {
        // TODO: Implement restore() method.
    }
}