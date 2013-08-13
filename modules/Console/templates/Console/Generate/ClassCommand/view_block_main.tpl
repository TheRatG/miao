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

	protected function _processData()
	{
	    throw new Exception( spritnf( 'Redeclare method "%s" in children classes', __METHOD__ ) );
	}

	protected function _setTemplateVariables()
	{
	    throw new Exception( spritnf( 'Redeclare method "%s" in children classes', __METHOD__ ) );
    }
}