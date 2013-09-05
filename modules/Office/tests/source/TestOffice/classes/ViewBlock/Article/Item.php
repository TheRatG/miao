<?php
/** 
 * User: vpak
 * Date: 05.09.13
 * Time: 13:00 
 */

namespace Miao\TestOffice\ViewBlock\Article;


class Item extends \Miao\TestOffice\ViewBlock implements \Miao\Office\Controller\ViewBlockInterface
{
    /**
     * (non-PHPdoc)
     * @see \Miao\Office\Controller\ViewBlockInterface::processData()
     */
    public function processData()
    {
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