<?php
/**
 *
 * @author %author%
 * @date %date%
 */

namespace %namespace%;

class %class% extends %parent% implements \Miao\Office\Controller\ViewInterface
{
    protected $_expires = 10;

    protected function _init()
	{
        $this->_initTemplateVariables();
        $this->_initHeaders();
	}

    /**
     * (non-PHPdoc)
     * @see \Miao\Office\Controller\ViewInterface
     */
	public function initializeBlock()
	{
    }

    protected function _initTemplateVariables()
    {
    }

    protected function _initHeaders()
    {
        $this->getOffice()->getHeader()->set( 'X-UA-Compatible', 'X-UA-Compatible: IE=Edge,chrome=1' );
        $this->getOffice()->getHeader()->set( 'X-Accel-Expires', 'X-Accel-Expires: ' . $this->_expires );
        $this->getOffice()->getHeader()->set( 'Cache-Control', 'Cache-Control: max-age=' . $this->_expires );
    }
}