<?php
class Miao_Autoload_Plugin_Standart extends Miao_Autoload_Plugin
{
	public function getFilenameByClassName( $className )
	{
		$items = explode( '_', $className );
		$cntItems = count( $items );
		if ( count( $items ) < 2 || $items[ 0 ] != $this->getName() )
		{
			return '';
		}
		$tmp = '%s/modules/%s/classes/%s.class.php';
		if ( 'Test' == $items[ $cntItems - 1 ] )
		{
			array_pop( $items );
			$cntItems = count( $items );

			$tmp = '%s/modules/%s/tests/classes/%s.class.Test.php';
		}

		if ( count( $items ) == 2 )
		{
			$items[ 2 ] = $items[ 1 ];
		}
		return sprintf( $tmp, $this->getLibPath(), $items[ 1 ],
			implode( '/', array_slice( $items, 2 ) ) );
	}
}