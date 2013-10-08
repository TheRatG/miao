<?php
/**
 *
 * @author %author%
 * @date %date%
 */

namespace %namespace%;

class %class% extends %parent% implements \Miao\Office\Controller\ActionInterface
{
    public function execute()
    {
        throw new Exception( sprintf( 'Redeclare method "%s" in children classes', __METHOD__ ) );
    }
}