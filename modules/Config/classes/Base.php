<?php
/**
 * User: vpak
 * Date: 02.09.13
 * Time: 15:52
 */
namespace Miao\Config;

use Miao\Config\Exception;

class Base
{
    const PATH_SEPARATOR = '.';

    private $_configData;

    public function __construct( array $configData )
    {
        $this->_configData = $configData;
    }

    /**
     * @param $path
     * @param null $default
     * @return array|null
     * @throws Exception\PathNotFound
     * @throws Exception\InvalidPath
     */
    public function get( $path, $default = null )
    {
        if ( empty( $path ) )
        {
            throw new \Miao\Config\Exception\InvalidPath( $path, 'path is empty' );
        }
        if ( substr( $path, 0, 1 ) !== self::PATH_SEPARATOR )
        {
            $path = self::PATH_SEPARATOR . $path;
        }

        $result = $this->_configData;
        if ( $path !== self::PATH_SEPARATOR )
        {
            $keys = explode( self::PATH_SEPARATOR, $path );
            for ( $i = 1, $c = count( $keys ); $i < $c; $i++ )
            {
                if ( empty( $keys[ $i ] ) )
                {
                    throw new \Miao\Config\Exception\InvalidPath( $path, 'path contains empty key' );
                }
                if ( !is_array( $result ) || !isset( $result[ $keys[ $i ] ] ) )
                {
                    if ( is_null( $default ) )
                    {
                        throw new \Miao\Config\Exception\PathNotFound( $path );
                    }
                    $result = $default;
                    break;
                }
                $result = $result[ $keys[ $i ] ];
            }
        }
        return $result;
    }

    /**
     * @param $pathMain
     * @param array $configData
     */
    public function add( $pathMain, array $configData )
    {
        $this->_configData[ $pathMain ] = $configData;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->_configData;
    }
}