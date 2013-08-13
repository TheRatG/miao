<?php
/**
 *
 * @author %author%
 * @date %date%
 */

namespace %namespace%;

class %class% extends %parent%
{
    protected $_expires = 10;

    protected function _init()
	{
        $this->_initTmplVariables();
        $this->_initHeaders();
	}

    /**
     * There is place for define view bloks
     * @example $this->_initBlock( 'Meta', 'Project_FrontOffice_ViewBlock_Meta', array( 'index.tpl' ) );
     */
	protected function _initializeBlock()
	{
    }

    protected function _initTmplVariables()
    {
    }

    protected function _initHeaders()
    {
        $this->getOffice()->getHeader()->set( 'X-UA-Compatible', 'X-UA-Compatible: IE=Edge,chrome=1' );
        $this->getOffice()->getHeader()->set( 'X-Accel-Expires', 'X-Accel-Expires: ' . $this->_expires );
        $this->getOffice()->getHeader()->set( 'Cache-Control', 'Cache-Control: max-age=' . $this->_expires );
    }
}