<?php
/**
 * Facade
 */
namespace Miao;

/**
 * Class Application
 * @package Miao
 */
class Application
{
    const INSTANCE_DEFAULT_NAME = 'default';

    /**
     * @var array Application
     */
    static protected $_instance = array();

    /**
     * @var Application\Config
     */
    protected $_config;

    protected $_path;

    protected $_env;

    /**
     * @param array $configMain
     * @param array $configModules
     * @param string $name Instance name
     * @return \Miao\Application
     */
    static public function init( array $configMap, array $configMain, array $configModules = array(),
                                 $name = self::INSTANCE_DEFAULT_NAME )
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
     * @return null|Application
     */
    static public function getInstance( $name = self::INSTANCE_DEFAULT_NAME )
    {
        $result = null;
        if ( isset( self::$_instance[ $name ] ) )
        {
            $result = self::$_instance[ $name ];
        }
        return $result;
    }

    /**
     * @param string $name
     * @param bool $throwException
     * @return \Miao\Config\Base
     */
    public function getConfig( $name = '', $throwException = true )
    {
        $result = $this->_config->getObject( $name, $throwException );
        return $result;
    }

    /**
     * @return mixed
     */
    public function getEnv()
    {
        return $this->_env;
    }

    /**
     * @return \Miao\Path
     */
    public function getPath()
    {
        return $this->_path;
    }

    public function setConfig( $name, array $data )
    {
        $this->_config[ $name ] = new Config\Base( $data );
    }

    protected function __construct( array $configMap, array $configMain, array $configModules = array() )
    {
        $this->_config = new Application\Config( $configMain, $configModules );
        $this->_path = new \Miao\Path( $configMap['project_root'], $configMap['libs'] );
    }
}