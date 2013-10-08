<?php
/** 
 * User: vpak
 * Date: 03.09.13
 * Time: 14:45 
 */

namespace Miao\Office\Controller;


class Action extends \Miao\Office\Controller implements \Miao\Office\Controller\ActionInterface
{
    /**
     * @return string|void
     * @throws Exception
     */
    public function execute()
    {
        throw new Exception( sprintf( 'Redeclare method "%s" in children classes', __METHOD__ ) );
    }

    /**
     * Generate html content
     * @return string
     */
    public function generateContent()
    {
        $this->_init();
        $content = $this->execute();
        return $content;
    }
}