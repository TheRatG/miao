<?php
/**
 *
 * @author %author%
 * @date %date%
 */

namespace %namespace%;

/**
 * Common view bloks and properties defined in parent class,
 * always check parent::_initializeBlock()
 */
class %class% extends %parent%
{
    /**
     * There is place for define view bloks
     * @example $this->_initBlock( 'Meta', 'Project_FrontOffice_ViewBlock_Meta', array( 'index.tpl' ) );
     */
    protected function _initializeBlock()
	{
	    parent::_initializeBlock();
	}
}