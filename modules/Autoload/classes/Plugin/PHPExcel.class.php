<?php
class Miao_Autoload_Plugin_PHPExcel extends Miao_Autoload_Plugin
{
	public function getFilenameByClassName( $className )
	{
		$items = explode( '_', $className );
		if ( !count( $items ) || $items[ 0 ] != 'PHPExcel' )
		{
			return '';
		}
		return sprintf( '%s/PHPExcel.php', $this->getLibPath() );
	}
}