<?php
require_once 'Exception.class.php';
require_once 'Glue/Token.class.php';

class Miao_Glue
{
	private $_files;

	static public function getFileList( $dir, $mask = '*.php' )
	{
		$result = self::rglob( $mask, 0, $dir );
		return $result;
	}

	static public function rglob( $pattern = '*', $flags = 0, $path = '' )
	{
		$paths = glob( $path . '*', GLOB_MARK | GLOB_ONLYDIR | GLOB_NOSORT );
		$files = glob( $path . $pattern, $flags );
		foreach ( $paths as $path )
		{
			$files = array_merge( $files, self::rglob( $pattern, $flags, $path ) );
		}
		return $files;
	}

	public function __construct( array $files )
	{
		$this->_checkFiles( $files );
		$this->_files = $files;
	}

	public function weld( $resultFilename, $compact )
	{
		$content = "<?php\n";
		$res = file_put_contents( $resultFilename, $content );

		foreach ( $this->_files as $filename )
		{
			$res = $this->_append( $resultFilename, $filename, $compact );
			if ( false == $res )
			{
				break;
			}
		}

		$result = false;
		if ( false !== $res )
		{
			$result = true;
		}
		return $result;
	}

	private function _checkFiles( array $files )
	{
		$message = '';
		if ( !empty( $files ) )
		{
			foreach ( $files as $filename )
			{
				if ( !file_exists( $filename ) )
				{
					$message = sprintf( 'File %s does not exists', $filename );
					break;
				}
			}
		}
		else
		{
			$message = 'Invalid param files^ must be not empty array';
		}

		if ( !empty( $message ) )
		{
			throw new Miao_Glue_Exception( $message );
		}
	}

	private function _append( $resultFilename, $filename, $compact )
	{
		$token = Miao_Glue_Token::factory( $filename );
		$content = $token->toString( $compact ) . "\n";
		$res = file_put_contents( $resultFilename, $content, FILE_APPEND );
		$result = false;
		if ( false !== $res )
		{
			$result = true;
		}
		return $result;
	}
}