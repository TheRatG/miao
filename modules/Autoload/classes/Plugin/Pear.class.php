<?php
class Miao_Autoload_Plugin_Pear extends Miao_Autoload_Plugin
{
	public function getFilenameByClassName( $className )
	{
		$filename = $this->getLibPath() . DIRECTORY_SEPARATOR . str_replace( '_', DIRECTORY_SEPARATOR, $className ) . '.php';
		return $filename;
	}
}