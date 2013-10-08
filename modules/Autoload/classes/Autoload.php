<?php
namespace Miao;

use Miao\Autoload\Plugin;
use Miao\Autoload\Exception;

require_once 'Exception.php';
require_once 'PluginInterface.php';
require_once 'Plugin.php';
require_once 'ClassInfo.php';

class Autoload
{
    static private $_instance;

    /**
     * @var array
     */
    private $_registerList = array();

    /**
     * @var array
     */
    private $_history = array();

    /**
     * @return Autoload
     */
    static public function getInstance()
    {
        if ( is_null( self::$_instance ) )
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Registration libraries autoload
     * @param array $libs
     * @return bool
     * @throws Exception
     */
    static public function init( array $libs )
    {
        $self = self::getInstance();
        foreach ( $libs as $libItem )
        {
            $self->checkConfigItem( $libItem );
            if ( 0 !== strcasecmp( $libItem[ 'plugin' ], 'None' ) )
            {
                $self->registerItem( $libItem[ 'name' ], $libItem[ 'plugin' ], $libItem[ 'path' ] );
            }
        }
        $res = spl_autoload_register( array( $self, 'autoload' ), true );
        if ( !$res )
        {
            throw new Exception( 'Miao autoload did not register' );
        }
        return $res;
    }

    /**
     * @param $className
     * @return string
     */
    static public function getFilenameByClassName( $className )
    {
        $self = self::getInstance();
        $result = '';
        $self->_history = array();

        foreach ( $self->getRegisterList() as $item )
        {
            /**
             * @var $item Plugin
             */
            $filename = $item->getFilenameByClassName( $className );
            $self->_history[ ] = $filename;
            if ( file_exists( $filename ) )
            {
                $result = $filename;
                break;
            }
        }
        return $result;
    }

    static public function classExists( $className )
    {
        $self = self::getInstance();
        try
        {
            $result = $self->autoload( $className );
        }
        catch ( \Miao\Autoload\Exception\FileNotFound $e )
        {
            $result = false;
        }
        return $result;
    }

    static public function throwFileNotFoundException( $message )
    {
        throw new \Miao\Autoload\Exception\FileNotFound( $message );
    }

    static public function throwClassNotFoundException( $message )
    {
        throw new \Miao\Autoload\Exception\ClassNotFound( $message );
    }

    public function checkConfigItem( array $libItem )
    {
        $requireAttr = array( 'name', 'path', 'plugin' );
        foreach ( $requireAttr as $paramName )
        {
            if ( !isset( $libItem[ $paramName ] ) )
            {
                $message = sprintf(
                    'Invalid config item (%s), does not exists param (%s)', print_r( $libItem, true ), $paramName
                );
                throw new \Miao\Autoload\Exception\InvalidConfig( $message );
            }
        }

        if ( !file_exists( $libItem[ 'path' ] ) )
        {
            $message = sprintf( 'Invalid config item (path): file (%s) does not exists', $libItem[ 'path' ] );

            throw new \Miao\Autoload\Exception\InvalidConfig( $message );
        }

        return true;
    }

    public function registerItem( $name, $plugin, $libPath )
    {
        $index = $this->_getIndex( $name );
        $className = $plugin;
        //if you'll use your plugin
        if ( !class_exists( $plugin, false ) )
        {
            $className = '\\Miao\\Autoload\\Plugin\\' . $plugin;
        }
        $plugin = new $className( $name, $libPath );
        $this->registerPlugin( $index, $plugin );
    }

    public function registerPlugin( $name, Plugin $plugin )
    {
        $this->_registerList[ $name ] = $plugin;
    }

    public function autoload( $className )
    {
        $filename = self::getFilenameByClassName( $className );
        $result = false;
        if ( !empty( $filename ) && file_exists( $filename ) )
        {
            require_once $filename;
            if ( !class_exists( $className, false ) && !interface_exists( $className, false ) )
            {
                $message = sprintf( 'Class/interface (%s) not found (%s)', $className, $filename );
                self::throwClassNotFoundException( $message );
            }
            else
            {
                $result = true;
            }
        }
        else
        {
            $message = sprintf( 'File not found for class "%s": %s', $className, print_r( $this->_history, true ) );
            self::throwFileNotFoundException( $message );
        }
        return $result;
    }

    /**
     * @return array Plugin
     */
    public function getRegisterList()
    {
        return $this->_registerList;
    }

    /**
     * @param $name
     * @return \Miao\Autoload\Plugin|null
     */
    public function getPlugin( $name )
    {
        $index = $this->_getIndex( $name );
        $result = null;
        if ( isset( $this->_registerList[ $index ] ) )
        {
            $result = $this->_registerList[ $index ];
        }
        return $result;
    }

    protected function _getIndex( $name )
    {
        $result = strtolower( trim( $name ) );
        return $result;
    }

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }
}