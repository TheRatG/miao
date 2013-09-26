<?php
/**
 * @author vpak
 * @date 2013-09-26 17:00:43
 */

namespace Miao\Session;

class Container implements \ArrayAccess, \Iterator
{
    private $_container = array();

    private $_namespace = false;

    protected $_position = 0;

    protected $_keys = array();

    static public function getNick( $namespace )
    {
        $result = '__Miao-Session::' . $namespace;
        return $result;
    }

    public function __construct( $namespace )
    {
        $this->_namespace = $namespace;
        $this->_load();
    }

    public function &get( $offset )
    {
        $this->_load();
        return $this->_container[ $offset ];
    }

    public function set( $offset, &$value )
    {
        $this->_load();
        $this->_container[ $offset ] = & $value;
        $this->_save();
    }

    public function remove( $offset )
    {
        unset( $this->_container[ $offset ] );
        $nick = self::getNick( $this->_namespace );
        unset( $_SESSION[ $nick ] );
    }

    public function save()
    {
        $this->_save();
    }

    public function clear()
    {
        $this->_container = array();
        $this->_position = 0;
        $this->_keys = array();
        $this->_save();
    }

    // {{{ magic -------------------------------------
    public function __isset( $offset )
    {
        $this->_load();
        return isset( $this->_container[ $offset ] );
    }

    public function __set( $offset, $value )
    {
        if ( $value instanceof Miao_Session_Namespace_Reference )
        {
            $this->set( $offset, $value->getReference() );
        }
        else
        {
            $this->set( $offset, $value );
        }
    }

    public function &__get( $offset )
    {
        return $this->get( $offset );
    }

    public function __unset( $offset )
    {
        $this->remove( $offset );
    }

    // }}} magic -------------------------------------

    // {{{ ArrayAccess --------------------------
    public function offsetExists( $offset )
    {
        return $this->__isset( $offset );
    }

    public function &offsetGet( $offset )
    {
        return $this->__get( $offset );
    }

    public function offsetSet( $offset, $value )
    {
        $this->__set( $offset, $value );
    }

    public function offsetUnset( $offset )
    {
        $this->__unset( $offset );
    }

    // }}} ArrayAccess --------------------------

    // {{{ Iterator ----------------------------
    public function rewind()
    {
        $this->_position = 0;
    }

    public function current()
    {
        $this->_load();
        $data = $this->_container;
        $count = count( $data );
        reset( $data );
        for ( $i = 0; $i < $count; $i++ )
        {
            if ( $i == $this->_position )
            {
                break;
            }
            next( $data );
        }
        return current( $data );
    }

    public function key()
    {
        $keys = array_keys( $this->_container );
        return $keys[ $this->_position ];
    }

    public function next()
    {
        ++$this->_position;
    }

    public function valid()
    {
        $this->_load();
        // проверка на существование по позиции
        $count = $this->count();
        if ( $this->_position < $count && $this->_position >= 0 )
        {
            return true;
        }
        return false;
    }

    public function count()
    {
        $this->_load();
        $result = count( $this->_container );
        return $result;
    }

    // }}} Iterator ----------------------------

    protected function _save()
    {
        $nick = self::getNick( $this->_namespace );
        $_SESSION[ $nick ] = serialize( $this->_container );
    }

    protected function _load()
    {
        $nick = self::getNick( $this->_namespace );
        if ( empty( $this->_container ) )
        {
            $this->_container = isset( $_SESSION[ $nick ] ) ? unserialize( $_SESSION[ $nick ] ) : false;
            if ( !is_array( $this->_container ) )
            {
                $this->_container = array();
                $this->_save();
            }
        }
    }
}