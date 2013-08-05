<?php
namespace Miao\Autoload;

require_once 'Plugin/Standart.php';

abstract class Plugin
{
    /**
     * @var string Library/framework name
     */
    protected $_name = '';

    /**
     * @var string Library/framework root directory
     */
    protected $_libPath = '';

    /**
     * @param string $name Library/framework name
     * @param string $libPath Library/framework root directory
     * @throws Exception
     */
    public function __construct( $name, $libPath )
    {
        if ( !file_exists( $libPath ) || !is_readable( $libPath ) )
        {
            $message = sprintf( 'Invalid param $libPath (%s): file does not exists or not readable', $libPath );
            throw new Exception( $message );
        }
        $this->_name = $name;
        $this->_libPath = $libPath;
    }

    /**
     * @return string
     */
    public function getLibPath()
    {
        return $this->_libPath;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param $className
     * @return string Filename
     */
    abstract public function getFilenameByClassName( $className );

    /**
     * Add new include path in position. Delete duplicates
     * @param $includePath "New include path"
     * @param bool $pos
     * @param string $currentIncludePath (default = get_include_path())
     * @return string
     */
    static public function addIncludePath( $includePath, $pos = false, $currentIncludePath = '' )
    {
        if ( empty( $currentIncludePath ) )
        {
            $currentIncludePath = get_include_path();
        }

        $curAr = explode( PATH_SEPARATOR, $currentIncludePath );
        $newAr = explode( PATH_SEPARATOR, $includePath );

        $curAr = array_unique( $curAr );
        $newAr = array_unique( $newAr );

        $curAr = array_diff( $curAr, $newAr );

        if ( false === $pos )
        {
            $pieces = array_merge( $curAr, $newAr );
        }
        else
        {
            $firstPart = array_slice( $curAr, 0, $pos );
            $secondPart = array_slice( $curAr, $pos );

            $pieces = array_merge( $firstPart, $newAr );
            $pieces = array_merge( $pieces, $secondPart );
        }

        $result = implode( PATH_SEPARATOR, $pieces );
        set_include_path( $result );
        return $result;
    }
}