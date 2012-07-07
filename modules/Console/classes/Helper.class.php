<?php

class Miao_Console_Helper
{
	/**
	 * Удаление директории и всех вложенных файлов
	 * @static
	 * @param $dir
	 * @return bool
	 */
	public static function delDir( $dir )
	{
		if ( is_dir( $dir ) )
		{
			chmod( $dir, 0777 );
			$objects = scandir( $dir );
			foreach ( $objects as $object )
			{
				if ( $object != "." && $object != ".." )
				{
					$path = $dir . DIRECTORY_SEPARATOR . $object;
					if ( filetype( $path ) == "dir" )
					{
						self::delDir( $path );
					}
					else
					{
						self::delFile( $path );
					}
				}
			}
			reset( $objects );
			return rmdir( $dir );
		}
		return false;
	}

	/**
	 * Создание директории, включая вложенные директории
	 * @static
	 * @param $dir
	 * @return bool
	 */
	public static function mkDir( $dir )
	{
		if ( is_dir( $dir ) )
		{
			return true;
		}
		return mkdir( $dir, 0775, true );
	}

	/**
	 * Создание файла, включая создание директорий
	 * @static
	 * @param $file
	 * @param string $content
	 * @return bool|int
	 */
	public static function mkFile( $file, $content = '' )
	{
		if ( is_file( $file ) )
		{
			return true;
		}
		$dir = pathinfo( $file, PATHINFO_DIRNAME );
		if ( self::mkDir( $dir ) )
		{
			return file_put_contents( $file, $content );
		}
		return false;
	}

	/**
	 * Удаление файла
	 * @static
	 * @param $file
	 * @return bool
	 */
	public static function delFile( $file )
	{
		if ( is_file( $file ) )
		{
			chmod( $file, 0777 );
			return unlink( $file );
		}
		return true;
	}

	/**
	 * Список файлов по шаблону ф-ции glob()
	 * @static
	 * @param $pattern
	 * @param int $flags
	 * @return array
	 */
	public static function fileList( $pattern, $flags = 0 )
	{
		$files = glob( $pattern, $flags );

		foreach ( glob( dirname( $pattern ) . '/*', GLOB_ONLYDIR|GLOB_NOSORT ) as $dir )
		{
			$files = array_merge( $files, self::fileList( $dir . '/' . basename( $pattern ), $flags ) );
		}

		return $files;
	}

	/**
	 * @static
	 * @param $source
	 * @param $target
	 * @return array
	 */
	static public function copyDir( $source, $target )
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
						$res = self::copyDir( $newSource, $newTarget );
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
}