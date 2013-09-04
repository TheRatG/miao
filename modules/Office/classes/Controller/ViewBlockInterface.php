<?php
/**
 * User: vpak
 * Date: 03.09.13
 * Time: 14:53
 */

namespace Miao\Office\Controller;

interface ViewBlockInterface
{
    /**
     * Prepare data for template
     * @return void
     */
    public function processData();

    /**
     * Assign template variables
     * @return array
     */
    public function initTemplateVariables();
}