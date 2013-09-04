<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vpak
 * Date: 02.09.13
 * Time: 15:01
 */

namespace Miao\Path;

class Resolver
{
    /**
     * @var array
     */
    protected $_libs = array();

    static public function factory( array $map )
    {
        if ( !isset( $map[ 'project_root' ] ) )
        {
            $msg = 'Invalid $map, must content project_root item';
            throw new Exception( $msg );
        }
        if ( !isset( $map[ 'libs' ] ) )
        {
            $msg = 'Invalid $map, must content libs array item';
            throw new Exception( $msg );
        }
        $result = new self( $map[ 'project_root' ], $map[ 'libs' ] );
        return $result;
    }

    public function __construct( $projectRoot, array $libs )
    {
        $this->_projectRoot = $projectRoot;
        $this->addLibByArray( $libs );
    }

    public function addLibByArray( array $data )
    {
        foreach ( $data as $value )
        {
            if ( !array_key_exists( 'name', $value ) || !array_key_exists( 'path', $value ) )
            {
                $msg = 'Invalid lib data, must content item and path keys';
                throw new Exception( $msg );
            }

            $name = $value[ 'name' ];
            $path = $value[ 'path' ];
            $this->addLib( $name, $path );
        }
    }

    public function addLib( $name, $path )
    {
        $this->_libs[ $name ] = $path;
    }

    /**
     * @param string $string
     * @return mixed
     * @throws \Miao\Path\Exception
     */
    public function getRootDir( $string = '' )
    {
        if ( !$string )
        {
            $result = $this->_projectRoot;
        }
        else
        {
            $classInfo = \Miao\Autoload\ClassInfo::parse( $string );
            $name = $classInfo->getLib();
            $result = $this->_getDir( $name );
        }
        return $result;
    }

    /**
     * Return module dir by __CLASS__ or __METHOD__
     * @param $string
     * @return string
     * @throws \Miao\Path\Exception
     */
    public function getModuleDir( $string )
    {
        if ( empty ( $string ) )
        {
            $msg = 'Invalid param $string, must be not empty';
            throw new Exception( $msg );
        }
        $classInfo = \Miao\Autoload\ClassInfo::parse( $string );

        try
        {
            $this->_getDir( $classInfo->getLib() );
            $result = sprintf( '%s/modules/%s', $this->_getDir( $classInfo->getLib() ), $classInfo->getModule() );
        }
        catch ( \Miao\Path\Exception $e )
        {
            $loader = \Miao\App::getInstance()->getObject( \Miao\App::INSTANCE_COMPOSER_LOADER_NICK );
            if ( $loader )
            {
                $filename = $loader->findFile( ltrim( $string, '\\' ) );
                if ( $filename )
                {
                    $result = substr( $filename, 0, strpos( $filename, '/classes/') );
                }
            }
        }
        return $result;
    }

    /**
     * @param $className
     * @return string
     */
    public function getTemplateDir( $className )
    {
        $classInfo = \Miao\Autoload\ClassInfo::parse( $className );
        $delimiter = $classInfo->isOldFashion() ? '_' : '\\';
        $ar = explode( $delimiter, $className );
        $cnt = count( $ar );

        $moduleDir = $this->getModuleDir( $className );
        $result = $moduleDir . DIRECTORY_SEPARATOR . 'templates';
        if ( $cnt > 2 )
        {
            if ( $classInfo->isView() )
            {
                $result .= DIRECTORY_SEPARATOR . 'View';
            }
            else
            {
                $result .= DIRECTORY_SEPARATOR . implode( DIRECTORY_SEPARATOR, array_slice( $ar, 2 ) );
            }
        }
        return $result;
    }

    /**
     * Generate sources dir for tests
     * @param string $string Use __METHOD__
     * @return string
     * @throws \Miao\Path\Exception
     */
    public function getTestSourcesDir( $string )
    {
        if ( empty ( $string ) )
        {
            $msg = 'Invalid param $string, must be not empty';
            throw new Exception( $msg );
        }
        $ar = explode( '::', $string );
        $moduleRoot = $this->getModuleDir( $string );

        $className = $ar[ 0 ];
        $classInfo = \Miao\Autoload\ClassInfo::parse( $ar[ 0 ] );

        $classNamePath = array();
        if ( $classInfo->isOldFashion() )
        {
            $classNamePath = explode( '_', $className );
            array_shift( $classNamePath );
            if ( count( $classNamePath ) > 2 )
            {
                array_shift( $classNamePath );
            }
            if ( 'Test' === $classNamePath[ count( $classNamePath ) - 1 ] )
            {
                array_pop( $classNamePath );
            }
        }
        else
        {
            $className = $classInfo->getClass( true );
            $classNamePath = explode( '\\', $className );
        }
        $classNamePath = implode( DIRECTORY_SEPARATOR, $classNamePath );
        $classNamePath = rtrim( $classNamePath, '\\/' );

        $methodName = '';
        if ( isset( $ar[ 1 ] ) )
        {
            $methodName = $ar[ 1 ];
            $methodName = ltrim( $methodName, 'provider' );
        }

        $result = $moduleRoot . '/tests/sources/' . $classNamePath;
        $result = rtrim( $result, '\\/' );
        $result .= DIRECTORY_SEPARATOR . lcfirst( $methodName );
        $result = rtrim( $result, '\\/' );

        return $result;
    }

    /**
     * @param $name
     * @return string
     * @throws \Miao\Path\Exception
     */
    protected function _getDir( $name )
    {
        if ( isset( $this->_libs[ $name ] ) )
        {
            $result = $this->_libs[ $name ];
        }
        else
        {
            $msg = sprintf( 'Lib name (%s) undefined', $name );
            throw new Exception( $msg );
        }
        return $result;
    }
}