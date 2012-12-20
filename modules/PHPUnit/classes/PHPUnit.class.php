<?php
/**
 *
 * Helper function for test
 *
 * @author vpak
 *
 */
class Miao_PHPUnit
{
	/**
	 *
	 * Get source path
	 *
	 * @param unknown_type $classFilename
	 * @param unknown_type $methodName
	 */
	static public function getSourceFolder( $methodName )
	{
		$ar = explode( '::', $methodName );

		$className = $ar[ 0 ];

		$path = Miao_Path::getInstance();
		$moduleRoot = $path->getModuleRoot( $className );

		$classNamePath = explode( '_', $className );
		array_shift( $classNamePath );
		if ( count( $classNamePath ) > 2 )
		{
			array_shift( $classNamePath );
		}
		if ( 'Test' === $classNamePath[ count( $classNamePath ) - 1 ] )
		{
			array_pop( $classNamePath );
		}
		$classNamePath = implode( DIRECTORY_SEPARATOR, $classNamePath );
		$classNamePath = rtrim( $classNamePath, '\\/' );

		$methodName = '';
		if ( isset( $ar[ 1 ] ) )
		{
			$methodName = $ar[ 1 ];
			$methodName = ltrim( $methodName, 'provider' );
		}

		$result = $moduleRoot . '/tests/sources/' . $classNamePath;
		$result = rtrim( $result, '\\/' );
		$result .= DIRECTORY_SEPARATOR . lcfirst( $methodName );
		$result = rtrim( $result, '\\/' );

		return $result;
	}

	static public function copyr( $source, $target )
	{
		$result = array();
		if ( is_dir( $source ) )
		{
			//special for test
			if ( basename( $source ) == '.svn' )
			{

			}
			else
			{
				if ( !file_exists( $target ) )
				{
					mkdir( $target );
					$result[] = $target;
				}
				$d = dir( $source );
				while ( FALSE !== ( $entry = $d->read() ) )
				{
					if ( $entry == '.' || $entry == '..' )
					{
						continue;
					}
					$newSource = $source . '/' . $entry;
					$newTarget = $target . '/' . $entry;

					if ( is_dir( $newSource ) )
					{
						$res = self::copyr( $newSource, $newTarget );
						$result = array_merge( $result, $res );
						continue;
					}
					copy( $newSource, $newTarget );
					chmod( $newTarget, 0775 );
					$result[] = $newTarget;
				}
				$d->close();
			}
		}
		else
		{
			copy( $source, $target );
			chmod( $target, 0775 );
			$result[] = $target;
		}
		return $result;
	}

	static public function rmdirr( $dir )
	{
		if ( is_dir( $dir ) )
		{
			$objects = scandir( $dir );
			foreach ( $objects as $object )
			{
				if ( $object != "." && $object != ".." )
				{
					if ( filetype( $dir . "/" . $object ) == "dir" )
						self::rmdirr( $dir . "/" . $object );
					else
						unlink( $dir . "/" . $object );
				}
			}
			reset( $objects );
			rmdir( $dir );
		}
	}

	static public function getTempPath()
	{
		$result = Miao_Config::Main()->get( '/build/paths/tmp' );
		return $result;
	}

	static public function getFileList( $rootDir, &$files = array() )
	{
		if( is_dir( $rootDir ) )
		{
			$dir = opendir( $rootDir );
			while( ( $fileName = readdir( $dir ) ) !== false )
			{
				if( $fileName != '.' && $fileName != '..' && $fileName != '.svn' )
				{
					$path = $rootDir . '/' . $fileName;
					if( is_file( $path ) )
					{
						$files[] = $path;
					}
					if( is_dir( $path ) )
					{
						self::getFileList( $path, $files );
					}
				}
			}
			closedir( $dir );
		}
	}
}