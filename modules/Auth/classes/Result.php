<?php
/**
 * @author vpak
 * @date 2013-09-26 11:21:25
 */

namespace Miao\Auth;

class Result
{
    /**
     * General Failure
     */
    const FAILURE = 0;

    /**
     * Failure due to identity not being found.
     */
    const FAILURE_IDENTITY_NOT_FOUND = -1;

    /**
     * Failure due to identity being ambiguous.
     */
    const FAILURE_IDENTITY_AMBIGUOUS = -2;

    /**
     * Failure due to invalid credential being supplied.
     */
    const FAILURE_CREDENTIAL_INVALID = -3;

    /**
     * Failure due to uncategorized reasons.
     */
    const FAILURE_UNCATEGORIZED = -4;

    /**
     * Authentication success.
     */
    const SUCCESS = 1;

    /**
     * Authentication result code
     * @var int
     */
    protected $_code;

    /**
     * The identity used in the authentication attempt
     * @var mixed
     */
    protected $_identity;

    /**
     * An array of string reasons why the authentication attempt was unsuccessful
     * If authentication was successful, this should be an empty array.
     * @var array
     */
    protected $_messages;

    protected $_login;

    protected $_options = array();

    protected $_createTime = null;

    /**
     * Sets the result code, identity, and failure messages
     * @param  int $code
     * @param  mixed $identity
     * @param $login
     * @param $options
     * @param  array $messages
     * @return \Miao\Auth\Result
     */
    public function __construct( $code, $identity, $login, $options, array $messages = array() )
    {
        $code = ( int )$code;

        if ( $code < self::FAILURE_UNCATEGORIZED )
        {
            $code = self::FAILURE;
        }
        elseif ( $code > self::SUCCESS )
        {
            $code = 1;
        }

        $this->_code = $code;
        $this->_identity = $identity;
        $this->_login = $login;
        $this->_options = $options;
        $this->_messages = $messages;
        $this->_createTime = time();
    }

    /**
     * Returns whether the result represents a successful authentication attempt
     * @return boolean
     */
    public function isValid()
    {
        return ( $this->_code > 0 ) ? true : false;
    }

    /**
     * getCode() - Get the result code for this authentication attempt
     * @return int
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * Returns the identity used in the authentication attempt
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->_identity;
    }

    /**
     * @return string $_login
     */
    public function getLogin()
    {
        return $this->_login;
    }

    public function getOption( $key )
    {
        $result = null;
        if ( array_key_exists( $key, $this->_options ) )
        {
            $result = $this->_options[ $key ];
        }
        return $result;
    }

    /**
     * @return string $_options
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * @param array $options
     */
    public function setOptions( $options )
    {
        $this->_options = $options;
    }

    /**
     * @return string Create object time
     */
    public function getCreateTime()
    {
        return $this->_createTime;
    }

    /**
     * Returns an array of string reasons why the authentication attempt was unsuccessful
     * If authentication was successful, this method returns an empty array.
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }
}