<?php
/**
 *
 * @author vpak
 * @date 2013-09-04 16:06:33
 */

namespace Miao\TestOffice;

class ViewBlock extends \Miao\Office\Controller\ViewBlock implements \Miao\Office\Controller\ViewBlockInterface
{
    /**
     * (non-PHPdoc)
     * @see \Miao\Office\Controller\ViewBlockInterface::processData()
     */
	public function processData()
	{
	    throw new \Exception( sprintf( 'Redeclare method "%s" in children classes', __METHOD__ ) );
	}

    /**
     * (non-PHPdoc)
     * @see \Miao\Office\Controller\ViewBlockInterface::initTemplateVariables()
     */
    public function initTemplateVariables()
	{
	    throw new \Exception( sprintf( 'Redeclare method "%s" in children classes', __METHOD__ ) );
    }
}