<?php
/**
 * @author vpak
 * @date 2013-08-13 16:24:03
 */

namespace Miao\Log\Filter;

interface FilterInterface
{
    /**
     * Returns TRUE to accept the message, FALSE to block it.
     * @param  array $event event data
     * @return boolean accepted?
     */
    public function accept( $event );
}