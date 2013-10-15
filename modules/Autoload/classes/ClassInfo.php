<?php
/**
 * User: vpak
 * Date: 02.09.13
 * Time: 16:09
 */

namespace Miao\Autoload;

class ClassInfo
{
    const TYPE_OBJECT_REQUEST_RESOURCE = 'Resource';

    const TYPE_OBJECT_REQUEST_VIEW = 'View';

    const TYPE_OBJECT_REQUEST_VIEWBLOCK = 'ViewBlock';

    const TYPE_OBJECT_REQUEST_ACTION = 'Action';

    private $_parsedString;

    private $_lib;

    private $_module;

    private $_class;

    private $_isTest = false;

    private $_isOldFashion = false;

    private $_delimiter;

    private $_isView = false;

    private $_isViewBlock = false;

    private $_isAction = false;

    private $_namespace;

    /**
     * @param bool $short
     * @return string
     */
    public function getClass( $short = false )
    {
        $result = $this->_class;
        if ( $short )
        {
            $ar = explode( $this->getDelimiter(), $result );
            if ( count( $ar ) > 2 )
            {
                $result = implode( $this->getDelimiter(), array_slice( $ar, 2 ) );
            }
            else
            {
                $result = $ar[ 1 ];
            }
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function getLib()
    {
        return $this->_lib;
    }

    /**
     * @return mixed
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * @return mixed
     */
    public function getParsedString()
    {
        return $this->_parsedString;
    }

    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * @return bool
     */
    public function isTest()
    {
        return $this->_isTest;
    }

    /**
     * @return mixed
     */
    public function getDelimiter()
    {
        return $this->_delimiter;
    }

    /**
     * @return boolean
     */
    public function isView()
    {
        return $this->_isView;
    }

    /**
     * @return boolean
     */
    public function isViewBlock()
    {
        return $this->_isViewBlock;
    }

    /**
     * @return boolean
     */
    public function isAction()
    {
        return $this->_isAction;
    }

    /**
     * @return bool
     */
    public function isOldFashion()
    {
        return $this->_isOldFashion;
    }

    /**
     * @param $string
     * @return ClassInfo
     */
    static public function parse( $string )
    {
        $result = new self( $string );
        return $result;
    }

    public function __construct( $string )
    {
        if ( empty( $string ) )
        {
            throw new Exception( 'Invalid param string' );
        }

        $this->_parsedString = $string;
        $this->_init();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = array(
            'parsedString' => $this->getParsedString(),
            'lib' => $this->getLib(),
            'module' => $this->getModule(),
            'class' => $this->getClass()
        );
        return $result;
    }

    protected function _init()
    {
        $string = $this->_parsedString;

        $string = ltrim( $string, '\\' );
        $pos = strpos( $string, '::' );
        if ( false !== $pos )
        {
            $string = substr( $string, 0, $pos );
        }

        $this->_isOldFashion = ( strpos( $string, '\\' ) === false );
        $this->_delimiter = $this->_isOldFashion ? '_' : '\\';
        $ar = explode( $this->_delimiter, $string );
        $this->_lib = $ar[ 0 ];
        $module = '';
        if ( isset( $ar[ 1 ] ) )
        {
            $module = $ar[ 1 ];
        }

        $this->_module = $module;
        $this->_class = implode( $this->_delimiter, $ar );

        $pos = strpos( strrev( $string ), 'tseT' );
        if ( 0 === $pos )
        {
            $this->_isTest = true;

            if ( count( $ar ) == 2 )
            {
                $this->_module = substr( $module, 0, strpos( $module, 'Test' ) );
            }
        }

        array_pop( $ar );
        $this->_namespace = implode( $this->_delimiter, $ar );

        $this->_initOffice();
    }

    protected function _initOffice()
    {
        if ( false !== strpos(
                $this->_class, 'Office' . $this->_delimiter . self::TYPE_OBJECT_REQUEST_VIEWBLOCK . $this->_delimiter
            )
        )
        {
            $this->_isViewBlock = true;
        }
        else if ( false !== strpos(
                $this->_class, 'Office' . $this->_delimiter . self::TYPE_OBJECT_REQUEST_VIEW . $this->_delimiter
            )
        )
        {
            $this->_isView = true;
        }
        else if ( false !== strpos(
                $this->_class, 'Office' . $this->_delimiter . self::TYPE_OBJECT_REQUEST_ACTION . $this->_delimiter
            )
        )
        {
            $this->_isAction = true;
        }
    }
}