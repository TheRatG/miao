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

    public function get( $path, $default = null )
    {
        if ( empty( $path ) )
        {
            throw new Exception\InvalidPath( $path, 'path is empty' );
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
                    throw new Exception\InvalidPath( $path, 'path contains empty key' );
                }
                if ( !is_array( $result ) || !isset( $result[ $keys[ $i ] ] ) )
                {
                    if ( is_null( $default ) )
                    {
                        throw new Exception\PathNotFound( $path );
                    }
                    $result = $default;
                    break;
                }
                $result = $result[ $keys[ $i ] ];
            }
        }
        return $result;
    }

    public function add( $pathMain, array $configData )
    {
        $this->_configData[ $pathMain ] = $configData;
    }

    public function toArray()
    {
        return $this->_configData;
    }
}