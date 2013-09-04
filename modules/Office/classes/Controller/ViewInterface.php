<?php
/**
 * User: vpak
 * Date: 03.09.13
 * Time: 14:47
 */

namespace Miao\Office\Controller;

interface ViewInterface
{
    /**
     * There is place for init viewblock
     * @example
     * <code>
     * $menuBlock = new \Project\FrontOffice\ViewBlock\Menu();
     * $this->_initBlock( 'MenuTop', $menuBlock, 'top.tpl' );
     * $this->_initBlock( 'MenuBottom', $menuBlock, 'bottom.tpl' );
     * </code>
     * @return void
     */
    public function initializeBlock();
}