<?php
/**
 *
 * @author vpak
 * @date 2013-09-04 16:06:44
 */

namespace Miao\TestOffice\View;

/**
 * Common view bloks and properties defined in parent class,
 * always check parent::_initializeBlock()
 */
class Article extends \Miao\TestOffice\View implements \Miao\Office\Controller\ViewInterface
{
    /**
     * (non-PHPdoc)
     * @see \Miao\Office\Controller\ViewInterface
     */
    public function initializeBlock()
	{
	    parent::initializeBlock();
	}
}