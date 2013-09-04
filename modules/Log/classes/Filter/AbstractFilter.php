<?php
/**
 * @author vpak
 * @date 2013-08-13 16:23:53
 */

namespace Miao\Log\Filter;

class AbstractFilter
{
    /**
     * Validate and optionally convert the config to array
     * @param  array|\Miao\Config $config Miao_Config or Array
     * @return array
     * @throws \Miao\Log\Exception
     */
    static protected function _parseConfig( $config )
    {
        if ( !is_array( $config ) )
        {
            throw new \Miao\Log\Exception( 'Configuration must be an array or instance of \Miao\Config' );
        }
        return $config;
    }
}