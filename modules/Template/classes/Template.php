<?php
/**
 * @author vpak
 * @date 2013-08-14 10:04:36
 */

namespace Miao;

class Template
{
    /**
     * @var string Default templates folder
     */
    protected $_templatesDir;

    /**
     * @var bool If it enable, exceptions messages will show in the result of fetch.
     */
    protected $_debugMode = true;

    /**
     * @var \Miao\Log
     */
    protected $_log;

    public function __construct( $templatesDir, $debugMode = true, \Miao\Log $log = null )
    {
        $this->setTemplatesDir( $templatesDir );
        $this->debugMode( $debugMode );
        $this->setLog( $log );
    }

    public function __destruct()
    {
        unset( $this->_log );
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

        $this->_templatesDir = $templatesDir;
    }

    /**
     * @return string Return default templates folder
     */
    public function getTemplatesDir()
    {
        return $this->_templatesDir;
    }

    /**
     * @param \Miao\Log $log
     */
    public function setLog( $log )
    {
        $this->_log = $log;
    }

    /**
     * @return \Miao\Log
     */
    public function getLog()
    {
        if ( is_null( $this->_log ) )
        {
            $this->_log = \Miao\Log::factory();
        }
        return $this->_log;
    }

    public function fetch( $templateName )
    {
        $absoluteFilename = $this->getAbsoluteFilename( $templateName );
        $templateContents = $this->_returnParsedTemplate( $absoluteFilename );
        return $templateContents;
    }

    public function getAbsoluteFilename( $template )
    {
        $result = $this->getTemplatesDir() . DIRECTORY_SEPARATOR . $template;
        return $result;
    }

    /**
     * Includes template with given absolute filename and returns parsed
     * content.
     * MUST NOT be used in any place - only for in_class usage.
     * If exception is thrown inside template - exception text will be returned
     * in debug mode.
     * @param $absoluteFilename
     * @return string
     * @throws \Exception|Template\Exception\Critical
     * @throws \Exception
     */
    protected function _returnParsedTemplate( $absoluteFilename )
    {
        $resultUnbelievableNameForVar = '';
        try
        {
            $this->_checkFile( $absoluteFilename );
            $resultUnbelievableNameForVar .= $this->_startBlock();

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

            $this
                ->getLog()
                ->log(
                    $this->_exceptionToString( $e, $absoluteFilename ), Miao_Log::ERR
                );

            if ( !$this->getConsumeException() )
            {
                throw $e;
            }

            if ( $this->debugMode() )
            {
                $resultUnbelievableNameForVar .= $this->_exceptionToString(
                    $e, $absoluteFilename
                );
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
            if ( $this->debugMode() )
            {
                throw new \Miao\Template\Exception\OnFileNotFound( $absoluteFilename );
            }
            return false;
        }
        return true;
    }

    /**
     * Return as string transformed Exception information. May be extended in child classes for additional functionality.
     * @param \Exception $e
     * @param string $absoluteFilename
     * @return string
     */
    protected function _exceptionToString( \Exception $e, $absoluteFilename = '' )
    {
        $trace = $e->getTrace();
        $trace = array_slice( $trace, 0, 3 );

        $result = sprintf(
            "Uri: \"%s\". \nTemplate: %s\nMessage: %s\nTrace: %s", $_SERVER[ 'REQUEST_URI' ], $absoluteFilename,
            $e->getMessage(), print_r( $trace, true )
        );
        return $result;
    }
}