<?php
/**
 * @author vpak
 * @date 2013-08-12 16:01:18
 */

namespace Miao;

class Office
{
    static public function getTypesObjectRequest()
    {
        $result = array(
            \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_RESOURCE,
            \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
            \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEWBLOCK,
            \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_ACTION
        );
        return $result;
    }
}