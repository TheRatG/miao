<?php
/**
 *
 * @author vpak
 * @date 2013-09-04 16:06:47
 */

namespace Miao\TestOffice;

class Action extends \Miao\Office\Controller\Action implements \Miao\Office\Controller\ActionInterface
{
    public function execute()
    {
        throw new \Exception( sprintf( 'Redeclare method "%s" in children classes', __METHOD__ ) );
    }
}