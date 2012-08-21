<?php
ini_set( 'memory_limit', '1024M' );
class Miao_PHPUnit_Console
{

	private $_opts;

	private $_remainingArgs;

	private $_isFile = false;

	private $_isDir = false;

	private $_noRun = false;

	private $_name = '';

	private $_testSuite;

	/**
	 *
	 * @var Miao_Log
	 */
	private $_log;

	public function __construct( array $opts, array $remainingArgs )
	{
		$this->_opts = $opts;
		$this->_remainingArgs = $remainingArgs;

		$this->_init();
		$this->_testSuite = new PHPUnit_Framework_TestSuite();

		Miao_Session::getInstance()->start();
		$this->_log = Miao_Log::easyFactory( '', true, 7 );
	}

	public function getListFileListByModuleName( $moduleName )
	{
		$result = array();

		$path = dirname( $this->getFilenameByClassName( $moduleName ) );

		$pieces = explode( '_', $moduleName );
		$cnt = count( $pieces );
		$test = $pieces[ $cnt - 1 ];
		$suffix = '';

		if ( strcasecmp( $test, 'Test' ) == 0 )
		{
			$suffix = DIRECTORY_SEPARATOR . $pieces[ $cnt - 2 ];
		}
		else
		{
			if ( $cnt > 2 )
			{
				$suffix = DIRECTORY_SEPARATOR . $pieces[ $cnt - 1 ];
			}
		}
		$dir = $path . $suffix;

		$result = $this->_getFileList( $dir );
		return $result;
	}

	public function getFilenameByClassName( $className )
	{
		$ar = explode( '_', $className );
		$last = $ar[ count( $ar ) - 1 ];
		if ( 'Test' != $last )
		{
			$ar[] = 'Test';
		}
		$className = implode( '_', $ar );
		$filename = Miao_Autoload::getFilenameByClassName( $className );

		return $filename;
	}

	public function getListFileListByLibName( $libName )
	{
		try
		{
			$path = Miao_Path::getDefaultInstance();
			$dir = $path->getRootByLibName( $libName );
		}
		catch ( Miao_Path_Exception $e )
		{
			throw new Miao_PHPUnit_Exception( $e->getMessage() );
		}

		$result = array();
		if ( is_dir( $dir ) )
		{
			$result = $this->_getFileList( $dir );
		}
		return $result;
	}

	public function run()
	{
		if ( $this->_noRun )
		{
			$message = sprintf( 'Unit test canceled' );
			$this->_info( $message );
			return;
		}

		$message = sprintf( 'Runnig unit test with options: isFile %d, isDir %d', $this->_isFile, $this->_isDir );
		$this->_info( $message );

		// file test method
		if ( $this->_isFile )
		{
			$message = sprintf( 'File enabled, search filename by class name (%s)', $this->_name );
			$this->_info( $message );

			if ( is_file( $this->_name ) )
			{
				$filename = $this->_name;
			}
			else
			{
				$filename = $this->getFilenameByClassName( $this->_name );
			}
			$this->_addTest( $filename );
		}
		if ( $this->_isDir )
		{
			$message = sprintf( 'Dir enabled, search filename by name (%s)', $this->_name );
			$this->_info( $message );

			$pieces = explode( '_', $this->_name );
			$piecesCnt = count( $pieces );

			$files = array();
			if ( $piecesCnt > 1 )
			{
				$message = sprintf( '$piecesCnt more 1, search test by module name' );
				$this->_info( $message );

				$files = $this->getListFileListByModuleName( $this->_name );
			}
			else
			{
				$message = sprintf( '$piecesCnt equal 1, search test by lib name' );
				$this->_info( $message );

				$files = $this->getListFileListByLibName( $this->_name );
			}

			$message = sprintf( "Test file list: %s", print_r( $files, true ) );
			$this->_info( $message );
			if ( !empty( $files ) )
			{
				$this->_testSuite->addTestFiles( $files );
			}
		}

		$testCnt = $this->_testSuite->count();

		if ( $testCnt )
		{
			$message = sprintf( 'Found (%d) tests', $testCnt );
			$this->_info( $message );

			$processIsolation = isset( $this->_opts[ 'processIsolation' ] ) ? $this->_opts[ 'processIsolation' ] : false;
			$runner = new PHPUnit_TextUI_TestRunner();
			$arguments = array(
				'processIsolation' => $processIsolation,
				'backupGlobals' => false );
			$runner->doRun( $this->_testSuite, $arguments );
		}
		else
		{
			$message = sprintf( 'No tests' );
			$this->_info( $message );
		}
	}

	private function _info( $message )
	{
		$this->_log->debug( $message );
	}

	private function _getFileList( $dir )
	{
		$files = array();
		Miao_PHPUnit::getFileList( $dir, $files );

		$result = array();
		foreach ( $files as $filename )
		{
			$mask = '.class.Test.php';
			$origLen = strlen( $filename );
			$pos = mb_strrpos( $filename, $mask );

			$cond = $pos == ( $origLen - strlen( $mask ) );
			if ( $cond )
			{
				$result[] = $filename;
			}
		}
		return $result;
	}

	private function _addTest( $filename )
	{
		$message = sprintf( 'Added file (%s) to test', $filename );
		$this->_info( $message );
		if ( !file_exists( $filename ) )
		{
			$message = sprintf( 'File for "%s" not found', $this->_name );
			$this->_info( $message );
			throw new Miao_PHPUnit_Exception( $message );
		}
		$this->_testSuite->addTestFile( $filename );
	}

	private function _init()
	{
		if ( isset( $this->_opts[ 'file' ] ) )
		{
			$this->_isFile = true;
		}
		if ( isset( $this->_opts[ 'dir' ] ) )
		{
			$this->_isDir = true;
		}
		$name = '';
		if ( isset( $this->_opts[ 'name' ] ) )
		{
			$name = $this->_opts[ 'name' ];
		}
		else
		{
			if ( isset( $this->_remainingArgs ) && is_array( $this->_remainingArgs ) )
			{
				$name = $this->_remainingArgs[ 0 ];
			}
		}

		if ( isset( $this->_opts[ 'no-run' ] ) && ( bool ) $this->_opts[ 'no-run' ] )
		{
			$this->_noRun = true;
		}

		if ( false == $this->_isFile && false == $this->_isDir )
		{
			$this->_isFile = true;
		}

		if ( empty( $name ) )
		{
			$message = 'The name of class or modules or lib is mandatory';
			$this->_info( $message );
			throw new Miao_PHPUnit_Exception( $message );
		}

		$this->_name = $name;
	}
}