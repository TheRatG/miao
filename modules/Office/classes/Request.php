<?php
/**
 * User: vpak
 * Date: 16.09.13
 * Time: 10:49
 */

namespace Miao\Office;

class Request
{
    const METHOD_GET = 'GET';

    const METHOD_HEAD = 'HEAD';

    const METHOD_POST = 'POST';

    const METHOD_PUT = 'PUT';

    const METHOD_DELETE = 'DELETE';

    /**
     * @var array
     */
    protected $_vars = array();

    protected $_method;

    static public function getMethod()
    {
        $result = ( isset( $_SERVER[ 'REQUEST_METHOD' ] ) ) ? $_SERVER[ 'REQUEST_METHOD' ] : self::METHOD_GET;
        return $result;
    }

    public function __construct( array $data )
    {
        $this->resetVars();
        $this->setValuesOf( $data );
    }

    /**
     * @param $name
     * @param null $defaultValue
     * @param bool $throwException
     * @return mixed
     * @throws Exception\OnFileNotFound
     */
    public function getValueOf( $name, $defaultValue = null, $throwException = true )
    {
        $result = $defaultValue;
        if ( $this->exists( $name ) )
        {
            $result = $this->_vars[ $name ];
        }
        elseif ( $throwException )
        {
            $msg = sprintf( 'Request variable with name "%s" was not received', $name );
            throw new \Miao\Office\Exception\OnFileNotFound( $msg );
        }
        return $result;
    }

    public function setValueOf( $name, $value )
    {
        $this->_vars[ $name ] = $value;
        return $this;
    }

    public function setValuesOf( array $data )
    {
        $this->_vars = array_merge_recursive( $this->_vars, $data );
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function exists( $name )
    {
        $result = array_key_exists( $name, $this->_vars );
        return $result;
    }

    public function resetVars()
    {
        $method = self::getMethod();
        if ( $method == self::METHOD_HEAD )
        {
            $method = self::METHOD_GET;
        }
        $this->_vars = $GLOBALS[ '_' . $method ];
        $this->_vars = array_merge_recursive( $this->_vars, $_FILES );
    }
}