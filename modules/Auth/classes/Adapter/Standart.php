<?php
/**
 * @author vpak
 * @date 2013-09-26 11:22:16
 */

namespace Miao\Auth\Adapter;

class Standart implements \Miao\Auth\Adapter\AdapterInterface
{
    protected $_data = array();

    public function __construct( array $data )
    {
        $this->_data = $data;
    }

    /**
     * @param scalar $login
     * @param scalar $password
     * @param array $options
     * @return \Miao\Auth\Result
     */
    public function login( $login, $password, array $options = array() )
    {
        $identity = $login;

        if ( array_key_exists( $login, $this->_data ) )
        {
            if ( $password == $this->_data[ $login ] )
            {
                $code = \Miao\Auth\Result::SUCCESS;
            }
            else
            {
                $code = \Miao\Auth\Result::FAILURE;
            }
        }
        else
        {
            $code = \Miao\Auth\Result::FAILURE_IDENTITY_NOT_FOUND;
        }
        $result = new \Miao\Auth\Result( $code, $identity, $login, $options );
        return $result;
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
        if ( $result->isValid() && array_key_exists( $result->getLogin(), $this->_data ) )
        {
            return true;
        }
        return false;
    }

    /**
     * @param string $identity
     * @return \Miao\Auth\Result|void
     */
    public function restore( $identity )
    {
        // TODO: Implement restore() method.
    }
}