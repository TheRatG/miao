<?php
/**
 * @author vpak
 * @date 2013-09-26 11:21:21
 */

namespace Miao;

class Auth
{
    /**
     * Persistent storage handler
     * @var \Miao\Auth\Storage\StorageInterface
     */
    protected $_storage = null;

    /**
     * @var \Miao\Auth\Adapter\AdapterInterface
     */
    protected $_adapter = null;

    protected $_check = null;

    /**
     * @param \Miao\Auth\Adapter\AdapterInterface $adapter
     * @param null $storage
     * @return \Miao\Auth
     */
    public function __construct( \Miao\Auth\Adapter\AdapterInterface $adapter, $storage = null )
    {
        if ( is_null( $storage ) )
        {
            $this->_storage = new \Miao\Auth\Storage\Session();
        }
        $this->setAdapter( $adapter );
    }

    /**
     * @return \Miao\Auth\Storage\StorageInterface $storage
     */
    public function getStorage()
    {
        return $this->_storage;
    }

    /**
     * @param \Miao\Auth\Storage\StorageInterface $storage
     */
    public function setStorage( \Miao\Auth\Storage\StorageInterface $storage )
    {
        $this->_storage = $storage;
    }

    /**
     * @throws Auth\Adapter\Exception
     * @return \Miao\Auth\Adapter\AdapterInterface $_adapter
     */
    public function getAdapter()
    {
        if ( is_null( $this->_adapter ) )
        {
            $message = 'Adapter was not define. Check your "miao.xml" section Auth';
            throw new \Miao\Auth\Adapter\Exception( $message );
        }
        return $this->_adapter;
    }

    /**
     * @param \Miao\Auth\Adapter\AdapterInterface $adapter
     */
    public function setAdapter( \Miao\Auth\Adapter\AdapterInterface $adapter )
    {
        $this->_adapter = $adapter;
    }

    /**
     * Returns true if and only if an identity is available from storage
     * @return boolean
     */
    public function hasIdentity()
    {
        $result = false;
        $identity = $this->getIdentity();
        if ( $identity )
        {
            $result = true;
        }
        return $result;
    }

    /**
     * Returns the identity from storage or null if no identity is available
     * @return mixed|null
     */
    public function getIdentity()
    {
        $result = null;
        $authRes = $this->getResult();
        if ( !$authRes )
        {
            $authRes = $this
                ->getAdapter()
                ->restore();
            if ( $authRes && $authRes instanceof \Miao\Auth\Result && $authRes->isValid() )
            {
                $this
                    ->getStorage()
                    ->write( $authRes );
            }
        }
        if ( $authRes && $authRes instanceof \Miao\Auth\Result )
        {
            $check = $this->_check( $authRes );
            if ( $check )
            {
                $result = $authRes->getIdentity();
            }
            else
            {
                $this->clearResult();
            }
        }
        return $result;
    }

    /**
     * @return \Miao\Auth\Result|NULL
     */
    public function getResult()
    {
        $storage = $this->getStorage();
        if ( $storage->isEmpty() )
        {
            return null;
        }
        return $storage->read();
    }

    /**
     * Clears the identity from persistent storage
     * @return void
     */
    public function clearResult()
    {
        $this
            ->getStorage()
            ->clear();
        $this->_check = null;
    }

    /**
     * @param scalar $login
     * @param scalar $password
     * @param array $options
     * @return \Miao\Auth\Result
     */
    public function login( $login, $password, array $options = array() )
    {
        $result = $this->_adapter->login( $login, $password, $options );
        if ( $this->hasIdentity() )
        {
            $this->clearResult();
        }
        if ( $result->isValid() )
        {
            $this
                ->getStorage()
                ->write( $result );
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function logout()
    {
        $result = $this->_adapter->logout( $this->getResult() );
        $this->clearResult();
        return$result;
    }

    protected function _check( $authResult )
    {
        if ( is_null( $this->_check ) )
        {
            $this->_check = $this->_adapter->check( $authResult );
        }
        return $this->_check;
    }
}