<?php
/**
 *
 * @author vpak
 * @date 2013-09-04 16:06:44
 */

namespace Miao\TestOffice\View;

/**
 * Common view blocks and properties defined in parent class,
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

        $menuViewBlock = new \Miao\TestOffice\ViewBlock\Menu();
        $menuViewBlock->setTemplateFilename( 'top.tpl' );
        $this->initBlock( 'MenuTop', $menuViewBlock );

        $viewBlock = new \Miao\TestOffice\ViewBlock\Article\Item();
        $this->initBlock( 'Article', $viewBlock );

        $menuViewBlock->setTemplateFilename( 'bottom.tpl' );
        $this->initBlock( 'MenuBottom', $menuViewBlock );
	}
}