<?php
/**
 * User: vpak
 * Date: 04.09.13
 * Time: 17:33
 */

namespace Miao\Template;

class Native
{
    /**
     * @var string Default templates folder
     */
    protected $_templatesDir;

    /**
     * @var bool If it enable, exceptions messages will show in the result of fetch.
     */
    protected $_debugMode = null;

    /**
     * @var \Miao\Logger
     */
    protected $_logger;

    /**
     * @var array
     */
    protected $_templateVars = array();

    /**
     * @var bool
     */
    protected $_consumeException = true;

    public function __construct( $templatesDir, $debugMode = null, \Psr\Log\LoggerInterface $logger = null )
    {
        $config = \Miao\App::config( __CLASS__, false, false );
        if ( $config )
        {
            $configTemplateDir = $config->get( 'templateDir', false, false );
            $templatesDir = ( !is_null( $templatesDir ) ) ? $templatesDir : $configTemplateDir;

            $configDebugMode = $config->get( 'debugMode', false, false );
            $debugMode = ( !is_null( $debugMode ) ) ? $debugMode : $configDebugMode;
            if ( is_null( $logger )  )
            {
                $configLoggerFilename = $config->get( 'logger.filename', false, false );
                $configLogLevel = $config->get( 'logger.logLevel', \Monolog\Logger::DEBUG, false );
                $configLogLevel = $configLogLevel ? $configLogLevel : \Monolog\Logger::DEBUG;
                if ( $configLoggerFilename )
                {
                    $logger = \Miao\Logger::factory( $configLoggerFilename, null, $configLogLevel );
                }
            }
        }

        $this->setTemplatesDir( $templatesDir );
        $this->debugMode( $debugMode );
        $this->setLogger( $logger );
    }

    public function __destruct()
    {
        unset( $this->_logger );
    }

    /**
     * @param $state
     * @return bool If it enable, exceptions messages will show in the result of fetch.
     */
    public function debugMode( $state = null )
    {
        if ( !is_null( $state ) )
        {
            $this->_debugMode = (bool)$state;
        }
        return $this->_debugMode;
    }

    /**
     * @param string $templatesDir Setter
     * @throws \Miao\Template\Exception
     */
    public function setTemplatesDir( $templatesDir )
    {
        if ( empty( $templatesDir ) || !is_string( $templatesDir ) )
        {
            $msg = 'Invalid param $templatesDir, must be not empty and string';
            throw new \Miao\Template\Exception( $msg );
        }

        $this->_templatesDir = rtrim( $templatesDir, DIRECTORY_SEPARATOR );
    }

    /**
     * @return string Return default templates folder
     */
    public function getTemplatesDir()
    {
        return $this->_templatesDir;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger( $logger )
    {
        $this->_logger = $logger;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        if ( is_null( $this->_logger ) )
        {
            $this->_logger = \Miao\App::logger();
        }
        return $this->_logger;
    }

    /**
     * @return bool
     */
    public function getConsumeException()
    {
        return $this->_consumeException;
    }

    /**
     * @param $consumeException bool
     */
    public function setConsumeException( $consumeException )
    {
        $this->_consumeException = (bool)$consumeException;
    }

    public function fetch( $templateName )
    {
        $absoluteFilename = $this->getAbsoluteFilename( $templateName );
        $templateContents = $this->_returnParsedTemplate( $absoluteFilename );
        return $templateContents;
    }

    public function getAbsoluteFilename( $template, $useSelfBaseDir = null )
    {
        if ( is_null( $useSelfBaseDir ) )
        {
            $result = $this->getTemplatesDir() . DIRECTORY_SEPARATOR . ltrim( $template, DIRECTORY_SEPARATOR );
        }
        else if ( true === $useSelfBaseDir )
        {
            $result = $this->getTemplatesDir() . DIRECTORY_SEPARATOR . ltrim( $template, DIRECTORY_SEPARATOR );
        }
        else
        {
            $result = $template;
        }
        return $result;
    }

    /**
     * Setter for template variables.
     * @param string $templateVarName
     * @param mixed $templateVarValue
     */
    public function setValueOf( $templateVarName, $templateVarValue = null )
    {
        $this->_templateVars[ $templateVarName ] = $templateVarValue;
    }

    /**
     * Assigns value by array
     * @param array $data
     */
    public function setValueOfByArray( array $data )
    {
        foreach ( $data as $templateVarName => $templateVarValue )
        {
            $this->_templateVars[ $templateVarName ] = $templateVarValue;
        }
    }

    /**
     * @param $varName
     * @param null $defaultValue
     * @param bool $useNullAsDefault
     * @return mixed
     * @throws \Miao\Template\Exception\OnVariableNotFound
     */
    public function getValueOf( $varName, $defaultValue = null, $useNullAsDefault = false )
    {
        if ( !array_key_exists( $varName, $this->_templateVars ) )
        {
            if ( ( null === $defaultValue ) && ( false === $useNullAsDefault ) )
            {
                $msg = sprintf( 'Template variable (%s) not found', $varName );
                throw new \Miao\Template\Exception\OnVariableNotFound( $msg );
            }
            $this->_templateVars[ $varName ] = $defaultValue;
        }
        else if ( !isset( $this->_templateVars[ $varName ] ) )
        {
            $this->_templateVars[ $varName ] = $defaultValue;
        }
        return $this->_templateVars[ $varName ];
    }

    /**
     * Unset all template variables.
     */
    public function resetTemplateVariables()
    {
        unset( $this->_templateVars );
        $this->_templateVars = array();
    }

    public function includeTemplate( $templateFilename, $useSelfBaseDir = true, array $templateVars = array() )
    {
        if ( $templateVars )
        {
            $this->setValueOfByArray( $templateVars );
        }
        $templateFilename = $this->getAbsoluteFilename( $templateFilename, $useSelfBaseDir );
        return $this->_returnParsedTemplate( $templateFilename );
    }

    /**
     * Includes template with given absolute filename and returns parsed
     * content.
     * MUST NOT be used in any place - only for in_class usage.
     * If exception is thrown inside template - exception text will be returned
     * in debug mode.
     * @param $absoluteFilename
     * @return string
     * @throws \Exception|\Miao\Template\Exception\Critical
     * @throws \Exception
     */
    protected function _returnParsedTemplate( $absoluteFilename )
    {
        $resultUnbelievableNameForVar = '';
        try
        {
            $resultUnbelievableNameForVar .= $this->_startBlock();

            $this->_checkFile( $absoluteFilename );

            include( $absoluteFilename );

            $resultUnbelievableNameForVar .= $this->_endBlock();
        }
        catch ( \Miao\Template\Exception\Critical $e )
        {
            throw $e; // re-throw exception to the outer catch block
        }
        catch ( \Exception $e )
        {
            $resultUnbelievableNameForVar .= $this->_endBlock();

            $msg = $this->_exceptionToString( $e, $absoluteFilename );
            $this
                ->getLogger()
                ->error( $msg );

            if ( !$this->getConsumeException() )
            {
                throw $e;
            }
            if ( $this->debugMode() )
            {
                $resultUnbelievableNameForVar .= $msg;
            }
        }
        return $resultUnbelievableNameForVar;
    }

    /**
     * Starts any parsed block ( see beore ).
     * May be extended in child classes for additional functionality.
     * @return string
     */
    protected function _startBlock()
    {
        $result = '';
        ob_start();
        return $result;
    }

    /**
     * Ends any parsed block ( see beore ).
     * May be extended in child classes for additional functionality.
     * @return string
     */
    protected function _endBlock()
    {
        $buffer = ob_get_contents();
        ob_end_clean();

        $result = '';
        if ( false !== $buffer )
        {
            $result = $buffer;
        }
        return $result;
    }

    /**
     * Do some checks ( on existence and readability ) on file with given absolute filename.
     * May be extended in child classes for additional functionality.
     * @param $absoluteFilename
     * @return bool
     * @throws \Miao\Template\Exception\OnFileNotFound
     */
    protected function _checkFile( $absoluteFilename )
    {
        if ( ( !file_exists( $absoluteFilename ) )
            || ( !is_readable(
                $absoluteFilename
            ) )
        )
        {
            throw new \Miao\Template\Exception\OnFileNotFound( 'File not found: path = "' . $absoluteFilename . '"' );
        }
    }

    /**
     * Return as string transformed Exception information. May be extended in child classes for additional functionality.
     * @param \Exception $e
     * @param string $absoluteFilename
     * @return string
     */
    protected function _exceptionToString( \Exception $e, $absoluteFilename = '' )
    {
        $requestUri = isset( $_SERVER[ 'REQUEST_URI' ] ) ? $_SERVER[ 'REQUEST_URI' ] : 'console';

        $trace = $e->getTrace();
        $trace = array_slice( $trace, 0, 3 );

        $result = sprintf(
            "Uri: \"%s\". \nTemplate: %s\nMessage: %s\nTrace: %s", $requestUri, $absoluteFilename, $e->getMessage(),
            print_r( $trace, true )
        );
        return $result;
    }
}