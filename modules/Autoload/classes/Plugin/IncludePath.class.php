<?php
class Miao_Autoload_Plugin_IncludePath extends Miao_Autoload_Plugin
{
	public function __construct( $libPath )
	{
		parent::__construct( $libPath );
		self::addIncludePath( $libPath );
	}

	public function getFilenameByClassName( $className )
	{
		$pathRoot = $this->getLibPath();
		$partPath = str_replace( '_', DIRECTORY_SEPARATOR, $className );
		$result = $pathRoot . DIRECTORY_SEPARATOR . $partPath . '.php';

		if ( !file_exists( $result ) )
		{
			$result = '';
		}

		return $result;
	}
}