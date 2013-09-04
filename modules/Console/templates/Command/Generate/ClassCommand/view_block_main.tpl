<?php
/**
 *
 * @author %author%
 * @date %date%
 */

namespace %namespace%;

class %class% extends %parent%
{
    protected function _init()
	{
	    parent::_init();
	}

    /**
     * (non-PHPdoc)
     * @see \Miao\Office\Controller\ViewBlockInterface::processData()
     */
	public function processData()
	{
	    throw new Exception( spritnf( 'Redeclare method "%s" in children classes', __METHOD__ ) );
	}

    /**
     * (non-PHPdoc)
     * @see \Miao\Office\Controller\ViewBlockInterface::initTemplateVariables()
     */
    public function initTemplateVariables()
	{
	    throw new Exception( spritnf( 'Redeclare method "%s" in children classes', __METHOD__ ) );
    }
}