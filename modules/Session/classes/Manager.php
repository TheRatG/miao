<?php
/**
 * @author vpak
 * @date 2013-09-26 12:27:47
 */

namespace Miao\Session;

class Manager
{
    protected $_optionsMap = array(
        'save_path',
        'name',
        'save_handler',
        'gc_probability',
        'gc_divisor',
        'gc_maxlifetime',
        'serialize_handler',
        'cookie_lifetime',
        'cookie_path',
        'cookie_domain',
        'cookie_secure',
        'cookie_httponly',
        'use_cookies',
        'use_only_cookies',
        'referer_check',
        'entropy_file',
        'entropy_length',
        'cache_limiter',
        'cache_expire',
        'use_trans_sid',
        'bug_compat_42',
        'bug_compat_warn',
        'hash_function',
        'hash_bits_per_character'
    );

    /**
     * @var \Miao\Session\Handler\HandlerInterface
     */
    protected $_handler = null;

    /**
     * @var \Miao\Session\Handler\HandlerInterface[]
     */
    protected $_list = array();

    static public function factoryByConfig()
    {
        $app = \Miao\App::getInstance();
        $result = $app->getObject( \Miao\App::INSTANCE_SESSION_NICK, false );
        if ( !$result )
        {
            $config = \Miao\App::getInstance()
                ->config( __CLASS__ );
            $options = $config->get( 'options', false );
            if ( !$options )
            {
                $options = array();
            }
            $handlerConfig = $config->get( 'Handler', false );
            $handler = null;
            if ( !empty( $handlerConfig ) )
            {
                $handlerClassName = '\\Miao\\Session\\Handler\\' . key( $handlerConfig );
                $handler = \Miao\Config\Instance::get( $handlerClassName );
            }
            $result = new self( $options, $handler );
            $app->setObject( $result, \Miao\App::INSTANCE_SESSION_NICK );
        }
        return $result;
    }

    /**
     * @param array $options
     * @param Handler\HandlerInterface $handler
     */
    public function __construct( array $options = array(), \Miao\Session\Handler\HandlerInterface $handler = null )
    {
        $this->setOptions( $options );
        if ( $handler )
        {
            $this->setHandler( $handler );
        }
    }

    public function getContainer( $name )
    {
        if ( array_key_exists( $name, $this->_list ) )
        {
            $result = $this->_list[ $name ];
        }
        else
        {
            $this->_list[ $name ] = new \Miao\Session\Container( $name );
            $result = $this->_list[ $name ];
        }
        return $result;
    }

    public function getOptions()
    {
        $options = $this->_optionsMap;
        foreach ( $options as $name )
        {
            $result[ 'session.' . $name ] = $this->getOption( $name );
        }
        return $result;
    }

    public function getOption( $name )
    {
        $varName = 'session.' . strtolower( $name );
        $result = ini_get( $varName );
        return $result;
    }

    public function setOption( $name, $value )
    {
        $varName = strtolower( $name );
        if ( false !== array_search( $name, $this->_optionsMap ) )
        {
            $varName = 'session.' . $varName;
            $res = ini_set( $varName, $value );

            if ( false === $res )
            {
                $message = sprintf( 'Option %s can\'t be change', $name );
                throw new \Miao\Session\Exception( $message );
            }
        }
        else
        {
            $message = sprintf( 'Invalid option name %s', $name );
            throw new \Miao\Session\Exception( $message );
        }

        return $this;
    }

    public function setOptions( array $options )
    {
        foreach ( $options as $name => $value )
        {
            $this->setOption( $name, $value );
        }
        return $this;
    }

    /**
     * @return \Miao\Session\Handler\HandlerInterface|null
     */
    public function getHandler()
    {
        return $this->_handler;
    }

    /**
     * @param Handler\HandlerInterface $handler
     * @return $this
     */
    public function setHandler( \Miao\Session\Handler\HandlerInterface $handler )
    {
        $this->_handler = $handler;
        return $this;
    }

    /**
     * Get session ID
     * Proxies to {@link session_id()}
     * @return string
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * Regenerate id
     * Regenerate the session ID, using session save handler's
     * native ID generation Can safely be called in the middle of a session.
     * @param  bool $deleteOldSession
     * @return SessionManager
     */
    public function regenerateId( $deleteOldSession = true )
    {
        session_regenerate_id( (bool)$deleteOldSession );
        return $this;
    }

    /**
     * Does a session exist and is it currently active?
     * @return bool
     */
    public function sessionExists()
    {
        $sid = defined( 'SID' ) ? constant( 'SID' ) : false;
        if ( $sid !== false && $this->getId() )
        {
            return true;
        }
        if ( headers_sent() )
        {
            return true;
        }
        return false;
    }

    /**
     * Start session
     * if No session currently exists, attempt to start it. Calls
     * {@link isValid()} once session_start() is called, and raises an
     * exception if validation fails.
     * @return void
     */
    public function start()
    {
        if ( !$this->sessionExists() )
        {
            $saveHandler = $this->getHandler();
            if ( $saveHandler instanceof \Miao\Session\Handler\HandlerInterface )
            {
                // register the session handler with ext/session
                $this->registerSaveHandler( $saveHandler );
            }

            session_start();
        }
    }

    /**
     * Destroy/end a session
     * @param  array $options See {@link $defaultDestroyOptions}
     * @return void
     */
    public function destroy( array $options = null )
    {
        if ( $this->sessionExists() )
        {
            session_destroy();
        }
    }

    /**
     * Register Save Handler with ext/session
     * Since ext/session is coupled to this particular session manager
     * register the save handler with ext/session.
     * @param \Miao\Session\Handler\HandlerInterface $handler
     * @return bool
     */
    protected function registerSaveHandler( \Miao\Session\Handler\HandlerInterface $handler )
    {
        return session_set_save_handler(
            array( $handler, 'open' ), array( $handler, 'close' ), array( $handler, 'read' ),
            array( $handler, 'write' ), array( $handler, 'destroy' ), array( $handler, 'gc' )
        );
    }
}