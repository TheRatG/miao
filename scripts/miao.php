<?php





class Miao_Autoload
{
	static private $_instance;

	private $_registerList = array();
	private $_history = array();

	private function __construct()
	{

	}

	private function __clone()
	{

	}

	static public function getInstance()
	{
		if ( is_null( self::$_instance ) )
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	static public function register( array $autoloadConfig )
	{
		$auto = self::getInstance();
		foreach ( $autoloadConfig as $configItem )
		{
			$auto->checkConfigItem( $configItem );
			$auto->registerItem( $configItem[ 'name' ], $configItem[ 'plugin' ], $configItem[ 'path' ] );
		}
		spl_autoload_register( array( $auto, 'autoload' ), true, false );
	}

	static public function getFilenameByClassName( $className )
	{
		$auto = self::getInstance();
		$result = '';
		$auto->_history = array();

		foreach ( $auto->getRegisterList() as $item )
		{
			$filename = $item->getFilenameByClassName( $className );
			$auto->_history = $filename;
			if ( file_exists( $filename ) )
			{
				$result = $filename;
				break;
			}
		}
		return $result;
	}

	public function registerItem( $name, $plugin, $libPath )
	{
		$index = $this->_getIndex( $name );
		$className = 'Miao_Autoload_Plugin_' . $plugin;
		$this->_registerList[ $index ] = new $className( $libPath );
	}

	public function autoload( $className )
	{
		$filename = $this->getFilenameByClassName( $className );
		if ( !empty( $filename ) && file_exists( $filename ) )
		{
			require_once $filename;
			if ( !class_exists( $className, false ) && !interface_exists( $className, false ) )
			{
				$message = sprintf( 'Class (%s) not found (%s)', $className, $filename );
				$this->_throwException( $className, 'Miao_Autoload_Exception_ClassNotFound', $message );
			}
			else
			{
				return true;
			}
		}
		else
		{
			$message = sprintf( 'File not found for class "%s": %s', $className, print_r( $this->_history, true ) );
			$this->_throwException( $className, 'Miao_Autoload_Exception_FileNotFound', $message );
		}
	}

	public function getRegisterList()
	{
		return $this->_registerList;
	}

	public function getPlugin( $name )
	{
		$index = $this->_getIndex( $name );
		$result = null;
		if ( isset( $this->_registerList[ $index ] ) )
		{
			$result = $this->_registerList[ $index ];
		}
		return $result;
	}

	public function checkConfigItem( array $configItem )
	{
		$requireAttr = array( 'name', 'path', 'plugin' );
		foreach ( $requireAttr as $paramName )
		{
			if ( !isset( $configItem[ $paramName ] ) )
			{
				$message = sprintf( 'Invalid config item (%s), does not exists param (%s)', print_r( $configItem, true ), $paramName );
				throw new Miao_Autoload_Exception_InvalidConfig( $message );
			}
		}

		if ( !file_exists( $configItem[ 'path' ] ) )
		{
			$message = sprintf( 'Invalid config item (path): file (%s) doesn\'t exists', $configItem[ 'path' ] );

			throw new Miao_Autoload_Exception_InvalidConfig( $message );
		}

		return true;
	}

	protected function _throwException( $className, $exceptionClassName, $exceptionMessage = '' )
	{
		$evalString = sprintf( 'class %s
			{
				public function __construct()
				{
					$message = "%s";
					throw new %s( $message );
				}

				static function __callstatic( $m, $args )
				{
					$message = "%s";
					throw new %s( $message );
				}
			}
				', $className, addslashes( $exceptionMessage ), $exceptionClassName, addslashes( $exceptionMessage ), $exceptionClassName );
		return eval( $evalString );
	}

	private function _getIndex( $name )
	{
		$result = strtolower( trim( $name ) );
		return $result;
	}
}






class Miao_Autoload_Exception extends Exception
{
	public function __construct( $message, $code = 0, Exception $previous = NULL )
	{
		if ( !headers_sent() )
		{
			header( 'HTTP/1.1 500 Internal Server Error' );
		}
		parent::__construct( $message, $code, $previous );
	}
}
class Miao_Autoload_Name
{
	const T_CLASS = 1;
	const T_MODULE = 2;
	const T_LIB = 4;

	private $_type;

	private $_name;
	private $_lib;
	private $_module;
	private $_class;
	private $_isTest = false;
	private $_cnt;

	private $_path;

	
	public function getType()
	{
		return $this->_type;
	}

	
	public function getName()
	{
		return $this->_name;
	}

	
	public function setName( $name )
	{
		$this->_name = $name;
	}

	
	public function getLib()
	{
		return $this->_lib;
	}

	
	public function setLib( $lib )
	{
		$this->_lib = $lib;
	}

	
	public function getModule()
	{
		return $this->_module;
	}

	
	public function setModule( $module )
	{
		$this->_module = $module;
	}

	public function getClass()
	{
		return $this->_class;
	}

	
	public function isTest()
	{
		return $this->_isTest;
	}

	
	public function getCnt()
	{
		return $this->_cnt;
	}

	public function parse( $name )
	{
		if ( empty( $name ) )
		{
			throw new Miao_Autoload_Exception_InvalidClassName( $name, 'empty string' );
		}

		$ar = explode( '_', $name );

		$this->setName( $name );
		$this->setLib( $ar[ 0 ] );
		$module = '';
		if ( isset( $ar[ 1 ] ) )
		{
			$module = $ar[ 1 ];
		}
		if ( isset( $ar[ 2 ] ) )
		{
			$this->_class = implode( '_', array_slice( $ar, 2 ) );
		}
		$this->setModule( $module );
		$this->_cnt = count( $ar );

		switch ( $this->_cnt )
		{
			case 1:
				$this->_type = self::T_LIB;
				break;
			case 2:
				$this->_type = self::T_MODULE;
				break;
			default:
				$this->_type = self::T_CLASS;
				break;
		}

		if ( 'Test' === $ar[ $this->_cnt -1 ] )
		{
			$this->_isTest = true;
		}
	}

	public function toArray()
	{
		$result = array(
			'type' => $this->getType(),
			'name' => $this->getName(),
			'lib' => $this->getLib(),
			'module' => $this->getModule(),
			'class' => $this->getClass() );
		return $result;
	}
}









abstract class Miao_Autoload_Plugin
{
	protected $_libPath;

	public function __construct( $libPath )
	{
		if ( !file_exists( $libPath ) || !is_readable( $libPath ) )
		{
			$message = sprintf( 'Invalid param $libPath (%s): file doesn\'t exists or not readable', $libPath );
			throw new Miao_Autoload_Exception($message);
		}
		$this->_libPath = $libPath;
	}

	
	public function getLibPath()
	{
		return $this->_libPath;
	}

	abstract public function getFilenameByClassName( $className );

	
	static public function addIncludePath( $includePath, $pos = false, $currentIncludePath = '' )
	{
		if ( empty( $currentIncludePath ) )
		{
			$currentIncludePath = get_include_path();
		}

		$curAr = explode( PATH_SEPARATOR, $currentIncludePath );
		$newAr = explode( PATH_SEPARATOR, $includePath );

		$curAr = array_unique( $curAr );
		$newAr = array_unique( $newAr );

		$curAr = array_diff( $curAr, $newAr );

		$pieces = array();
		if ( false === $pos )
		{
			$pieces = array_merge( $curAr, $newAr );
		}
		else
		{
			$firstPart = array_slice( $curAr, 0, $pos );
			$secondPart = array_slice( $curAr, $pos );

			$pieces = array_merge( $firstPart, $newAr );
			$pieces = array_merge( $pieces, $secondPart );
		}

		$result = implode( PATH_SEPARATOR, $pieces );
		set_include_path( $result );
		return $result;
	}
}
class Miao_Autoload_Exception_ClassNotFound extends Miao_Autoload_Exception
{
}
class Miao_Autoload_Exception_FileNotFound extends Miao_Autoload_Exception
{

}
class Miao_Autoload_Exception_InvalidClassName extends Miao_Autoload_Exception
{
	public function __construct( $className, $reason )
	{
		$msg = sprintf( 'Invalid class name "%s": %s', $className, $reason );
		parent::__construct( $msg );
	}
}

class Miao_Autoload_Exception_InvalidConfig extends Miao_Autoload_Exception
{

}
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
class Miao_Autoload_Plugin_PHPUnit extends Miao_Autoload_Plugin
{
	public function __construct( $libPath )
	{
		parent::__construct( $libPath );
		self::addIncludePath( $libPath );

		require_once 'PHPUnit' . DIRECTORY_SEPARATOR . 'Autoload.php';
	}

	public function getFilenameByClassName( $className )
	{
		$items = explode( '_', $className );
		if ( count( $items ) < 2 || $items[ 0 ] != 'PHPUnit' )
		{
			return '';
		}
		$result = sprintf( '%s/%s.php', $this->getLibPath(), implode( '/', $items ) );
		return $result;
	}
}
class Miao_Autoload_Plugin_Standart extends Miao_Autoload_Plugin
{
	public function getFilenameByClassName( $className )
	{
		$items = explode( '_', $className );
		$cntItems = count( $items );
		if ( count( $items ) < 2 )
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
class Miao_Autoload_Plugin_Zend extends Miao_Autoload_Plugin
{
	public function __construct( $libPath )
	{
		parent::__construct( $libPath );
		self::addIncludePath( $libPath );
	}

	public function getFilenameByClassName( $className )
	{
		$items = explode( '_', $className );
		if ( count( $items ) < 2 || $items[ 0 ] != 'Zend' )
		{
			return '';
		}
		$result = sprintf( '%s/%s.php', $this->getLibPath(), implode( '/', $items ) );
		return $result;
	}
}
class Miao_Config_Base
{
	private $_configData;

	public function __construct( array $configData )
	{
		$this->_configData = $configData;
	}

	public function get( $path, $default = null )
	{
		if ( empty( $path ) )
		{
			throw new Miao_Config_Exception_InvalidPath( $path, 'path is empty' );
		}
		if ( substr( $path, 0, 1 ) !== '/' )
		{
			$path = '/' . $path;
		}

		$result = $this->_configData;
		if ( $path !== '/' )
		{
			$keys = explode( '/', $path );
			for( $i = 1, $c = count( $keys ); $i < $c; $i++ )
			{
				if ( empty( $keys[ $i ] ) )
				{
					throw new Miao_Config_Exception_InvalidPath( $path, 'path contains empty key' );
				}
				if ( !is_array( $result ) || !isset( $result[ $keys[ $i ] ] ) )
				{
					if ( is_null( $default ) )
					{
						throw new Miao_Config_Exception_PathNotFound( $path );
					}
					$result = $default;
					break;
				}
				$result = $result[ $keys[ $i ] ];
			}
		}
		return $result;
	}

	public function add( $pathMain, array $configData )
	{
		$this->_configData[ $pathMain ] = $configData;
	}

	public function toArray()
	{
		return $this->_configData;
	}
}
class Miao_Config
{
	const SECTION_NAME_BUILD = 'build';

	static public function Main()
	{
		$instance = self::_getDefaultInstance();

		$path = self::SECTION_NAME_BUILD;
		$result = $instance->_get( $path );
		return $result;
	}

	static public function Libs( $className )
	{
		$instance = self::_getDefaultInstance();

		$path = str_replace( '_', '/', $className );
		$result = $instance->_get( $path );
		return $result;
	}

	static public function Modules( $className )
	{
				return self::Libs( $className );
	}

	
	private $_base;

	
	private $_file;

	public function __construct()
	{
		$this->setBase( new Miao_Config_Base( array() ) );
		$this->_file = new Miao_Config_File();
	}

	
	public function getBase()
	{
		return $this->_base;
	}

	
	public function setBase( $base )
	{
		$this->_base = $base;
	}

	static private function _getDefaultInstance()
	{
		$index = 'Miao_Config::default';
		if ( !Miao_Registry::isRegistered( $index ) )
		{
			$result = new self();
			Miao_Registry::set( $index, $result );
		}
		else
		{
			$result = Miao_Registry::get( $index );
		}
		return $result;
	}

	private function _get( $path )
	{
		$base = $this->getBase();

		$result = null;
		try
		{
			$result = $base->get( $path );
		}
		catch ( Miao_Config_Exception_PathNotFound $e )
		{
			$ar = explode( '/', $path );
			$className = implode( '_', $ar );

			if ( self::SECTION_NAME_BUILD == $path )
			{
				$pathMain = $path;
				$configFilename = $this->_file->getFilenameMain();
				$configData = include $configFilename;
				$configData = $configData[ 'config' ];
			}
			else
			{
				$pathMain = $ar[ 0 ];
				$configFilename = $this->_file->getFilenameByClassName( $className );
				$configData = include $configFilename;
			}

			$base->add( $pathMain, $configData );
		}
		$configData = $base->get( $path );
		$result = new Miao_Config_Base( $configData );
		return $result;
	}
}
class Miao_Config_Exception extends Exception
{

}
class Miao_Config_File
{
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

class Miao_Config_Instance
{
	static public function get( $className, $paramSection = '__construct' )
	{
		$configObj = Miao_Config::Modules( $className );
		$params = $configObj->get( '/' . $paramSection );

		$rc = new ReflectionClass( $className );
		$result = $rc->newInstanceArgs( $params );

		return $result;
	}
}
class Miao_Config_Exception_InvalidPath extends Miao_Config_Exception
{
	public function __construct( $path, $reason )
	{
		$msg = sprintf( 'Invalid path "%s": %s', $path, $reason );
		parent::__construct( $msg );
	}
}
class Miao_Config_Exception_PathNotFound extends Miao_Config_Exception
{
	public function __construct( $path )
	{
		$msg = sprintf( 'Path "%s" not found', $path );
		parent::__construct( $msg );
	}
}
class Miao_Env
{
	static public function register( array $config )
	{
		$config = new Miao_Config_Base( $config );
		$initialazer = new Miao_Env_Initializer( $config );
		$initialazer->run();
	}
}

class Miao_Env_Initializer
{
	private $_config = null;

	
	public function __construct( Miao_Config_Base $config )
	{
		$this->_config = $config;
	}

	
	public function run()
	{
		$error_level = $this->_config->get( 'error_level', false );
		if ( is_numeric( $error_level ) )
		{
			$this->setErrorLevel( $error_level );
		}

		$default_timezone = $this->_config->get( 'default_timezone', false );
		if ( $default_timezone )
		{
			$this->setDefaultTimezone( $default_timezone );
		}

		$umask = $this->_config->get( 'umask', false );
		if ( $umask )
		{
			$this->setUmask();
		}

		$unregister_globals = $this->_config->get( 'unregister_globals', false );
		if ( $unregister_globals )
		{
			$this->unregisterGlobals();
		}

		$strip_global_slashes = $this->_config->get( 'strip_global_slashes', false );
		if ( $strip_global_slashes && version_compare( PHP_VERSION, '5.3.0' ) == false )
		{
			$this->stripGlobalSlashes();
		}
	}

	
	public function setErrorLevel( $level = null )
	{
		if ( null !== $level )
		{
			error_reporting( $level );
		}
		else
		{
			error_reporting( E_ALL | E_STRICT );
		}
		ini_set( 'display_errors', true );
		return $this;
	}

	
	public function setDefaultTimezone( $timezone = null )
	{
				if ( null === $timezone )
		{
			$timezone = 'GMT';
		}
		date_default_timezone_set( $timezone );
		return $this;
	}

	
	public function setUmask()
	{
		umask( 0 );
		return $this;
	}

	
	public function unregisterGlobals()
	{
		$rg = ini_get( 'register_globals' );
		if ( $rg === '' || $rg === '0' || strtolower( $rg ) === 'off' )
		{
			return $this;
		}

				if ( isset( $_REQUEST[ 'GLOBALS' ] ) || isset( $_FILES[ 'GLOBALS' ] ) )
		{
			exit( 'I\'ll have a steak sandwich and... a steak sandwich.' );
		}

		$noUnset = array(
			'GLOBALS',
			'_GET',
			'_POST',
			'_COOKIE',
			'_REQUEST',
			'_SERVER',
			'_ENV',
			'_FILES' );

		$input = array_merge( $_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset( $_SESSION ) && is_array( $_SESSION ) ? $_SESSION : array() );
		foreach ( $input as $k => $v )
		{
			if ( !in_array( $k, $noUnset ) && isset( $GLOBALS[ $k ] ) )
			{
				unset( $GLOBALS[ $k ] );
				unset( $GLOBALS[ $k ] ); 			}
		}

		return $this;
	}

	
	public function stripGlobalSlashes()
	{

		set_magic_quotes_runtime( 0 );

		if ( get_magic_quotes_gpc() )
		{
			$_GET = $this->_stripSlashesArray( $_GET );
			$_POST = $this->_stripSlashesArray( $_POST );
			$_COOKIE = $this->_stripSlashesArray( $_COOKIE );
			$_REQUEST = $this->_stripSlashesArray( $_REQUEST );
		}

		return $this;
	}

	
	protected function _initSet()
	{
		$upload_tmp_dir = $this->_config->get( 'upload_tmp_dir', false );
		if ( $upload_tmp_dir )
		{
			ini_set( 'upload_tmp_dir', $this->_config->upload_tmp_dir );
		}
	}

	
	protected function _stripSlashesArray( &$array )
	{
		return is_array( $array ) ? array_map( array(
			$this,
			'_stripSlashesArray' ), $array ) : stripslashes( $array );
	}
}

ini_set( 'memory_limit', '256M' );

class Miao_PHPUnit_Console
{
	private $_opts;
	private $_remainingArgs;

	private $_isFile = false;
	private $_isDir = false;
	private $_noRun = false;
	private $_name = '';
	private $_testSuite;

	public function __construct( array $opts, array $remainingArgs )
	{
		$this->_opts = $opts;
		$this->_remainingArgs = $remainingArgs;

		$this->_init();
		$this->_testSuite = new PHPUnit_Framework_TestSuite();
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

				if ( $this->_isFile )
		{
			$message = sprintf( 'File enabled, search filename by class name (%s)', $this->_name );
			$this->_info( $message );

			$filename = $this->getFilenameByClassName( $this->_name );
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

			$runner = new PHPUnit_TextUI_TestRunner();
			$arguments = array(
				'processIsolation' => false,
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
		echo $message . "\n";
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
class Miao_PHPUnit_Exception extends Exception
{

}

class Miao_PHPUnit
{
	
	static public function getSourceFolder( $methodName )
	{
		$ar = explode( '::', $methodName );

		$className = $ar[ 0 ];

		$path = Miao_Path::getDefaultInstance();
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
class Miao_Registry_Exception extends Exception
{

}

class Miao_Registry extends ArrayObject
{
	
	private static $_registry = null;

	
	public static function getInstance()
	{
		if ( self::$_registry === null )
		{
			self::$_registry = new self();
		}
		return self::$_registry;
	}

	
	public static function unsetInstance()
	{
		self::$_registry = null;
	}

	
	public static function get( $index )
	{
		$instance = self::getInstance();

		if ( !$instance->offsetExists( $index ) )
		{
			throw new Miao_Registry_Exception( "No entry is registered for key '$index'" );
		}

		return $instance->offsetGet( $index );
	}

	
	public static function set( $index, $value )
	{
		$instance = self::getInstance();
		$instance->offsetSet( $index, $value );
	}

	
	public static function isRegistered( $index )
	{
		if ( self::$_registry === null )
		{
			return false;
		}
		return self::$_registry->offsetExists( $index );
	}

	public static function toArray()
	{
		return self::$_registry;
	}

	
	public function __construct( $array = array(), $flags = parent::ARRAY_AS_PROPS )
	{
		parent::__construct( $array, $flags );
	}

	
	public function offsetExists( $index )
	{
		return array_key_exists( $index, $this );
	}
}
