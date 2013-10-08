<?php
/**
 * User: vpak
 * Date: 05.09.13
 * Time: 10:42
 */

namespace Miao\Office\Controller\View;

class Template extends \Miao\Template\Native
{
    /**
     * @var string
     */
    protected $_viewTemplateFilename;

    /**
     * @var string[]
     */
    protected $_viewBlockList = array();

    /**
     * @param string $viewTemplateFilename
     */
    public function setViewTemplateFilename( $viewTemplateFilename )
    {
        $this->_viewTemplateFilename = $viewTemplateFilename;
    }

    /**
     * @return string
     */
    public function getViewTemplateFilename()
    {
        return $this->_viewTemplateFilename;
    }

    /**
     * @param $name
     * @param \Miao\Office\Controller\ViewBlock $viewBlock
     * @throws \Exception|\Miao\Template\Exception\Critical
     * @throws Exception
     * @return $this
     */
    public function initBlock( $name, \Miao\Office\Controller\ViewBlock $viewBlock )
    {
        if ( array_key_exists( $name, $this->_viewBlockList ) )
        {
            $msg = sprintf( 'Place named "%s" already init', $name );
            throw new \Miao\Office\Controller\View\Exception( $msg );
        }
        try
        {
            $this->_viewBlockList[ $name ] = $viewBlock->generateContent();
        }
        catch ( \Miao\Template\Exception\Critical $e )
        {
            throw $e; // re-throw exception to the outer catch block
        }
        catch ( Exception $e )
        {
            $message = $this->_exceptionToString( $e );
            if ( $this->debugMode() )
            {
                $this->_viewBlockList[ $name ] = $message;
            }
        }
        return $this;
    }

    /**
     * @param $name
     * @param string $before
     * @param string $after
     * @return string
     */
    public function includeBlock( $name, $before = '', $after = '' )
    {
        $result = '';
        if ( array_key_exists( $name, $this->_viewBlockList ) )
        {
            $result = $this->_viewBlockList[ $name ];
        }
        $trmRes = trim( $result );
        if ( !empty( $trmRes ) )
        {
            $result = $before . $result . $after;
        }
        return $result;
    }
}