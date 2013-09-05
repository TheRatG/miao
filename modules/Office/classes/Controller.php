<?php
/** 
 * User: vpak
 * Date: 03.09.13
 * Time: 14:45 
 */

namespace Miao\Office;


abstract class Controller
{
    /**
     * Generate html content
     * @return string
     */
    abstract public function generateContent();
}