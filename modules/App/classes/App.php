<?php
/**
 * Facade
 * User: vpak
 * Date: 02.09.13
 * Time: 15:22
 */

namespace Miao;

class App
{
    const INSTANCE_DEFAULT_NICK = 'app:default';

    const INSTANCE_CONFIG_NICK = 'app:config';

    const INSTANCE_PATH_NICK = 'app:path';

    /**
     * @var array Application
     */
    static protected $_instance = array();

    /**
     * @var array
     */
    protected $_objects;

    /**
     * @param array $configMain
     * @param array $configModules
     * @param string $name Instance name
     * @return \Miao\App
     */
    static public function init( array $configMap, array $configMain, array $configModules = array(),
                                 $name = self::INSTANCE_DEFAULT_NICK )
    {
        if ( !isset( self::$_instance[ $name ] ) )
        {
            self::$_instance[ $name ] = new self( $configMap, $configMain, $configModules );
        }
        $result = self::$_instance[ $name ];
        return $result;
    }

    /**
     * @param string $name Instance application name (default = 'Main')
     * @return null|App
     */
    static public function getInstance( $name = self::INSTANCE_DEFAULT_NICK )
    {
        $result = null;
        if ( isset( self::$_instance[ $name ] ) )
        {
            $result = self::$_instance[ $name ];
        }
        return $result;
    }

    /**
     * @return \Miao\Path\Resolver
     */
    public function getPath()
    {
        return $this->getObject( self::INSTANCE_PATH_NICK );
    }

    /**
     * @param object $object
     * @param null $nick
     * @return $this
     */
    public function setObject( $object, $nick = null )
    {
        assert( is_object( $object ) );
        if ( !is_null( $nick ) )
        {
            assert( is_string( $nick ) );
        }
        else
        {
            $nick = get_class( $object );
        }

        $this->_objects[ $nick ] = $object;
        return $this;
    }

    public function getObject( $nick )
    {
        $result = null;
        if ( isset( $this->_objects[ $nick ] ) )
        {
            $result = $this->_objects[ $nick ];
        }
        else
        {
            $msg = sprintf( 'Object by nick %s not found', $nick );
            throw new \Miao\App\Exception( $msg );
        }
        return $result;
    }

    protected function __construct( array $configMap, array $configMain, array $configModules = array() )
    {
        $this->setObject( new App\Config( $configMain, $configModules ), self::INSTANCE_CONFIG_NICK );
        $this->setObject(
            new \Miao\Path\Resolver( $configMap[ 'project_root' ], $configMap[ 'libs' ] ), self::INSTANCE_PATH_NICK
        );
    }
}