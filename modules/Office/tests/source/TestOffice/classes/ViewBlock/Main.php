<?php
/**
 *
 * @author vpak
 * @date 2013-09-04 16:06:33
 */

namespace Miao\TestOffice\ViewBlock;

class Main extends \Miao\TestOffice\ViewBlock implements \Miao\Office\Controller\ViewBlockInterface
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