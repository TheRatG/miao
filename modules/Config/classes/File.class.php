<?php
class Miao_Config_File
{
	public function getFilenameProject()
	{
		$path = Miao_Path::getDefaultInstance();
		$result = $path->getRoot() . '/data/config.php';
		return $result;
	}

	public function getFilenameMain()
	{
		$path = Miao_Path::getDefaultInstance();
		$result = $path->getMainConfigFilename();
		return $result;
	}

	public function getFilenameByClassName( $className )
	{
		$path = Miao_Path::getDefaultInstance();
		$dir = $path->getModuleRoot( $className );
		$result = $this->makeConfigFileNameByDir( $dir );
		return $result;
	}

	public function makeConfigFileNameByDir( $dir )
	{
		$result = sprintf( '%s/data/config.php', $dir );
		return $result;
	}
}