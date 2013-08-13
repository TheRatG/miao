<?php
/**
 * @author vpak
 * @date 2013-08-13 16:24:50
 */

namespace Miao\Log\Formatter;

interface FormatterInterface
{
    /**
     * Formats data into a single line to be written by the writer.
     * @param  array $event    event data
     * @return string             formatted line to write to the log
     */
    public function format( $event );
}