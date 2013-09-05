<?php
/** 
 * User: vpak
 * Date: 05.09.13
 * Time: 16:17 
 */

namespace Miao\TestOffice\ViewBlock;


class Menu  extends \Miao\TestOffice\ViewBlock implements \Miao\Office\Controller\ViewBlockInterface
{
    /**
     * (non-PHPdoc)
     * @see \Miao\Office\Controller\ViewBlockInterface::processData()
     */
    public function processData()
    {
        //$position = $this->getParam( 'position' );
    }

    /**
     * (non-PHPdoc)
     * @see \Miao\Office\Controller\ViewBlockInterface::initTemplateVariables()
     */
    public function initTemplateVariables()
    {
        //@example $this->_setTmplVar( 'value', $this->_value );
    }
}