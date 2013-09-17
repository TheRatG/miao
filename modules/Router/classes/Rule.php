<?php
/**
 * User: vpak
 * Date: 13.09.13
 * Time: 17:33
 */

namespace Miao\Router;

class Rule
{
    const REWRITE_MODE_APACHE = 'apache';

    const REWRITE_MODE_NGINX = 'nginx';

    /**
     * @var string
     */
    private $_rule;

    /**
     * @var string
     */
    private $_controller;

    /**
     * @var string
     */
    private $_controllerType;

    /**
     * @var string
     */
    private $_method;

    /**
     * @var \Miao\Office\Factory
     */
    private $_officeFactory;

    /**
     * @var null
     */
    private $_description;

    /**
     * @var null
     */
    private $_prefix;

    /**
     * @var bool
     */
    private $_noRewrite;

    /**
     * @var \Miao\Router\Rule\Validator[]
     */
    private $_validators;

    /**
     * @var string[]
     */
    private $_parts = array();

    /**
     * @var array
     */
    private $_params = array();

    static protected $_rewriteRuleModeMasks = array(
        self::REWRITE_MODE_APACHE => array(
            'mask' => '^%s%s$',
            'rewrite' => '%s?%s',
            'start' => 'RewriteRule',
            'flags' => '[L,QSA]',
            'index' => 'index.php'
        ),
        self::REWRITE_MODE_NGINX => array(
            'mask' => '"^/?%s%s$"',
            'rewrite' => '/%s?%s',
            'start' => 'rewrite',
            'flags' => 'break;',
            'index' => 'index.php'
        )
    );

    /**
     * @param array $config
     * @return \Miao\Router\Rule
     */
    static public function factory( array $config )
    {
        $prefix = \Miao\Router::checkAndReturnParam( $config, 'prefix', '' );
        $type = \Miao\Router::checkAndReturnParam( $config, 'type' );
        $name = \Miao\Router::checkAndReturnParam( $config, 'name' );
        $rule = \Miao\Router::checkAndReturnParam( $config, 'rule' );
        $method = \Miao\Router::checkAndReturnParam( $config, 'method', '' );
        $desc = \Miao\Router::checkAndReturnParam( $config, 'desc', '' );
        $validators = \Miao\Router::checkAndReturnParam(
            $config, 'validators', array()
        );
        $noRewrite = \Miao\Router::checkAndReturnParam( $config, 'norewrite', '' );

        $result = new self( $rule, $name, $type, $method, $validators, $desc, $prefix, $noRewrite );
        return $result;
    }

    public function __construct( $rule, $controller, $controllerType, $method, array $validators = array(),
                                 $description = null, $prefix = null, $noRewrite = false )
    {
        $this->setRule( $rule );
        $this->setController( $controller );
        $this->setControllerType( $controllerType );
        $this->setMethod( $method );
        $this->setDescription( $description );
        $this->setPrefix( $prefix );
        $this->setNoRewrite( $noRewrite );

        $this->_init( $validators );
    }

    /**
     * @param mixed $controller
     */
    public function setController( $controller )
    {
        $this->_controller = $controller;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * @param $controllerType
     * @throws Rule\Exception
     */
    public function setControllerType( $controllerType )
    {
        if ( !$this->checkControllerType( $controllerType ) )
        {
            $message = sprintf( 'Invalid route type: %s', $controllerType );
            throw new \Miao\Router\Rule\Exception( $message );
        }
        $this->_controllerType = $controllerType;
    }

    /**
     * @return mixed
     */
    public function getControllerType()
    {
        return $this->_controllerType;
    }

    /**
     * @param null $description
     */
    public function setDescription( $description )
    {
        $this->_description = $description;
    }

    /**
     * @return null
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @param mixed $method
     */
    public function setMethod( $method )
    {
        $this->_method = $method;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        if ( empty( $this->_method ) )
        {
            $this->_method = 'GET';
            if ( \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_ACTION == $this->getControllerType() )
            {
                $this->_method = 'POST';
            }
        }
        return $this->_method;
    }

    /**
     * @param boolean $noRewrite
     */
    public function setNoRewrite( $noRewrite )
    {
        $this->_noRewrite = $noRewrite;
    }

    /**
     * @return boolean
     */
    public function getNoRewrite()
    {
        return $this->_noRewrite;
    }

    /**
     * @param null $prefix
     */
    public function setPrefix( $prefix )
    {
        $this->_prefix = $prefix;
    }

    /**
     * @return null
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * @param mixed $rule
     */
    public function setRule( $rule )
    {
        $this->_rule = trim( $rule, '/' );
    }

    /**
     * @return mixed
     */
    public function getRule()
    {
        return $this->_rule;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * @return Rule\Validator[]
     * @throws Rule\Exception
     */
    public function getValidators()
    {
        if ( count( $this->_validators ) < count( $this->_parts ) )
        {
            $msg = sprintf(
                'Number of validators wrong (%s), parts (%s)', count( $this->_validators ), count( $this->_parts )
            );
            throw new \Miao\Router\Rule\Exception( $msg );
        }
        return $this->_validators;
    }

    /**
     * @param \Miao\Office\Factory $officeFactory
     */
    public function setOfficeFactory( $officeFactory )
    {
        $this->_officeFactory = $officeFactory;
    }

    /**
     * @return \Miao\Office\Factory
     */
    public function getOfficeFactory()
    {
        $result = $this->_officeFactory;
        if ( is_null( $this->_officeFactory ) )
        {
            $result = \Miao\App::getInstance()
                ->getObject( \Miao\App::INSTANCE_OFFICE_FACTORY_NICK, false );
            if ( !$result )
            {
                $result = new \Miao\Office\Factory();
            }
        }
        return $result;
    }

    public function addValidator( \Miao\Router\Rule\Validator $validator )
    {
        $this->_validators[ ] = $validator;
    }

    public function match( $uri, $method = null )
    {
        if ( empty( $method ) )
        {
            $method = \Miao\Office\Request::getMethod();
        }

        $result = false;
        if ( $method == $this->getMethod() )
        {
            $parts = explode( '/', trim( $uri, '/' ) );
            $result = array(
                $this->_getControllerRequestName() => $this->getController()
            );

            $cnt = count( $this->_validators );
            $partsIterator = 0;

            for ( $i = 0; $i < $cnt; $i++ )
            {
                $validator = $this->_validators[ $i ];
                if ( $validator instanceof \Miao\Router\Rule\Validator\Regexp )
                {
                    $slash = $validator->getSlash();
                    $part = implode(
                        '/', array_slice( $parts, $partsIterator, $slash + 1 )
                    );
                    $partsIterator += $slash + 1;
                }
                else
                {
                    $part = isset( $parts[ $partsIterator ] ) ? $parts[ $partsIterator ] : '';
                    $partsIterator++;
                }
                $check = $validator->test( $part );
                if ( false == $check )
                {
                    $result = $check;
                    break;
                }
                $paramIndex = $validator->getId();
                if ( $paramIndex )
                {
                    $result[ $paramIndex ] = $part;
                }
            }
            if ( count( $parts ) > $partsIterator )
            {
                $result = false;
            }
        }
        return $result;
    }

    public function makeUrl( array $params = array(), $method = null )
    {
        if ( empty( $method ) )
        {
            $method = \Miao\Office\Request::getMethod();
        }

        $uri = array();
        $parts = $this->_parts;
        foreach ( $parts as $paramName )
        {
            if ( $this->_isParam( $paramName ) )
            {
                $index = substr( $paramName, 1 );
                if ( isset( $params[ $index ] ) )
                {
                    $uri[ ] = $params[ $index ];
                    unset( $params[ $index ] );
                }
                else
                {
                    $message = sprintf(
                        'Require param (%s) does not exists in $params', $index
                    );
                    throw new \Miao\Router\Rule\Exception( $message );
                }
            }
            else
            {
                $uri[ ] = $paramName;
            }
        }
        $uri = implode( '/', $uri );
        $check = $this->match( $uri, $method );
        if ( false === $check )
        {
            $message = sprintf( 'Uri made (%s) but did not validate', $uri );
            throw new \Miao\Router\Rule\Exception( $message );
        }
        $query = http_build_query( $params );
        if ( !empty( $query ) )
        {
            $uri .= '?' . http_build_query( $params );
        }
        $uri = '/' . $uri;
        return $uri;
    }

    public function makeRewrite( $mode = 'apache', $addDesc = true )
    {
        if ( !in_array( $mode, array_keys( self::$_rewriteRuleModeMasks ) ) )
        {
            throw new \Miao\Router\Rule\Exception( sprintf(
                'Bad rewrite mode: %s', $mode
            ) );
        }

        if ( $this->getNoRewrite() )
        {
            $rule = sprintf( '# rule asks to skip it /%s', $this->_rule );
            return $rule;
        }

        $validators = $this->getValidators();
        $url = array();
        $params = array();
        $j = 1;
        foreach ( $this->_parts as $k => $part )
        {
            $pattern = $validators[ $k ]->getPattern();
            if ( $this->_isParam( $part ) && !empty( $pattern ) )
            {
                $part = substr( $part, 1 );
                $params[ $part ] = '$' . $j++;

                if ( false !== strpos( $pattern, '(' ) )
                {
                    $url[ ] = $pattern;
                }
                else
                {
                    $url[ ] = '(' . $pattern . ')';
                }
            }
            else
            {
                $url[ ] = $part;
            }
        }

        if ( $mode == 'nginx' && count( $params ) > 9 )
        {
            $rule = sprintf(
                '# error happened while generating rewrite for /%s (too many params)', $this->_rule
            );
        }
        else
        {
            $params[ $this->_getControllerRequestName() ] = $this->getController();
            if ( $this->getPrefix() )
            {
                $params[ '_prefix' ] = $this->getPrefix();
            }

            $suffix = substr( $this->_rule, -1 ) == '/' ? '/' : '';

            $mask = sprintf(
                self::$_rewriteRuleModeMasks[ $mode ][ 'mask' ], implode( '/', $url ), $suffix
            );
            $rewrite = sprintf(
                self::$_rewriteRuleModeMasks[ $mode ][ 'rewrite' ], self::$_rewriteRuleModeMasks[ $mode ][ 'index' ],
                str_replace( '%24', '$', urldecode( http_build_query( $params ) ) )
            );
            $start = self::$_rewriteRuleModeMasks[ $mode ][ 'start' ];
            $flags = self::$_rewriteRuleModeMasks[ $mode ][ 'flags' ];

            $desc = $addDesc ? sprintf(
                "# %s:%s%s\n", $this->getControllerType(), $this->getController(),
                $this->getDescription() ? ' ' . $this->getDescription() : ''
            ) : '';
            $rule = sprintf(
                '%s%s %s %s %s', $desc, $start, $mask, $rewrite, $flags
            );
        }
        return $rule;
    }

    public function checkControllerType( $controllerType )
    {
        $result = in_array(
            $controllerType, array(
                                  \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_ACTION,
                                  \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
                                  \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEWBLOCK
                             )
        );
        return $result;
    }

    protected function _isParam( $str )
    {
        return ':' == $str[ 0 ];
    }

    protected function _getControllerRequestName()
    {
        switch ( strtolower( $this->getControllerType() ) )
        {
            case strtolower( \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_ACTION ):
                $result = $this
                    ->getOfficeFactory()
                    ->getActionRequestName();
                break;

            case strtolower( \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW ):
                $result = $this
                    ->getOfficeFactory()
                    ->getViewRequestName();
                break;

            case strtolower( \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEWBLOCK ):
                $result = $this
                    ->getOfficeFactory()
                    ->getViewBlockRequestName();
                break;

            default:
                $msg = sprintf( 'Invalid controller type "%s"', $this->getControllerType() );
                throw new \Miao\Router\Rule\Exception( $msg );
        }
        return $result;
    }

    protected function _init( array $validators )
    {
        $rule = $this->getRule();
        $parts = explode( '/', $rule );
        foreach ( $parts as $key => $value )
        {
            if ( $value && ':' == $value[ 0 ] )
            {
                $id = substr( $value, 1 );
                $config = $this->_searchValidatorConfigById( $id, $validators );
                $this->_params[ ] = $id;
            }
            else
            {
                $config = array(
                    'id' => null,
                    'type' => 'Compare',
                    'str' => $value
                );
            }
            if ( !is_null( $config ) )
            {
                $validator = \Miao\Router\Rule\Validator::factory( $config );
                $this->_validators[ $key ] = $validator;
            }
        }
        $this->_parts = $parts;

        if ( count( $validators ) )
        {
            $message = sprintf(
                "Some validators did not find his part of uri (%s). Validators: %s", implode( '/', $this->_parts ),
                print_r( $validators, true )
            );
            throw new \Miao\Router\Rule\Exception( $message );
        }
    }

    protected function _searchValidatorConfigById( $id, &$validators )
    {
        $result = null;
        foreach ( $validators as $key => $config )
        {
            if ( !array_key_exists( 'id', $config ) )
            {
                throw new \Miao\Router\Rule\Exception( 'Invalid validator config item: must content attribute "id"' );
            }
            if ( $config[ 'id' ] == $id )
            {
                $result = $config;
                unset( $validators[ $key ] );
                break;
            }
        }
        return $result;
    }
}