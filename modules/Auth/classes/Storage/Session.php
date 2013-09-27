<?php
/**
 * @author vpak
 * @date 2013-09-26 11:22:04
 */

namespace Miao\Auth\Storage;

class Session implements \Miao\Auth\Storage\StorageInterface
{
    const NAMESPACE_DEFAULT = '\\Miao\\Auth\\Storage\\Session';

    const MEMBER_DEFAULT = 'storage';

    /**
     * Object to proxy $_SESSION storage
     * @var \Miao\Session\Container
     */
    protected $_session;

    /**
     * Session namespace
     * @var string
     */
    protected $_namespace;

    /**
     * Session object member
     * @var string
     */
    protected $_member;

    /**
     * Sets session storage options and initializes session namespace object
     * @param  string $namespace
     * @param  string $member
     * @return \Miao\Auth\Storage\Session
     */
    public function __construct( $namespace = self::NAMESPACE_DEFAULT, $member = self::MEMBER_DEFAULT )
    {
        $this->_session = \Miao\App::session()
            ->getContainer( $namespace );
        $this->_member = $member;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return !$this->_session->__isset( $this->_member );
    }

    public function read()
    {
        return $this->_session[ $this->_member ];
    }

    public function write( $contents )
    {
        $this->_session[ $this->_member ] = $contents;
    }

    public function clear()
    {
        $this->_session->remove( $this->_member );
    }
}