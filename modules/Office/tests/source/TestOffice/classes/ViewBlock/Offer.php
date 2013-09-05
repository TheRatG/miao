<?php
/** 
 * User: vpak
 * Date: 05.09.13
 * Time: 18:23 
 */

namespace Miao\TestOffice\ViewBlock;

class Offer extends \Miao\TestOffice\ViewBlock implements \Miao\Office\Controller\ViewBlockInterface
{
    protected $_number;

    /**
     * (non-PHPdoc)
     * @see \Miao\Office\Controller\ViewBlockInterface::processData()
     */
    public function processData()
    {
        $this->_number = $this->getParam( 'number' );
    }

    /**
     * (non-PHPdoc)
     * @see \Miao\Office\Controller\ViewBlockInterface::initTemplateVariables()
     */
    public function initTemplateVariables()
    {
        $this->setTmplVar( 'number', $this->_number );
    }
}