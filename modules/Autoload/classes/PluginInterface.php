<?php
namespace Miao\Autoload;

interface PluginInterface
{
    public function getFilenameByClassName( $className );
}