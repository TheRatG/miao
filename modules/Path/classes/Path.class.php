<?php
/**
 * Class resolves class names into template paths
 *
 * @author ptrofimov
 */
class Miao_Path
{
	private $_root;
	private $_mainConfigFilename;

	/**
	 *
	 * @var array
	 */
	private $_map;
	private $_pathNameObj;

	private static $_defaultindex = 'Miao_Path::default';

	static public function register( array $config )
	{
		$map = array();
		foreach ( $config[ 'libs' ] as $item )
		{
			$map[ $item[ 'name' ] ] = $item[ 'path' ];
		}
		$index = self::$_defaultindex;
		$result = new Miao_Path( $config[ 'project_root' ], $config[ 'main_config_filename' ], $map );
		Miao_Registry::set( $index, $result );
	}

	/**
	 *
	 *
	 * Enter description here ...
	 *
	 * @throws Miao_Path_Exception
	 * @return Miao_Path
	 */
	static public function getDefaultInstance()
	{
		$index = self::$_defaultindex;
		if ( !Miao_Registry::isRegistered( $index ) )
		{
			throw new Miao_Path_Exception( 'Default instance does\'t register.' );
		}
		$result = Miao_Registry::get( $index );
		return $result;
	}

	/**
	 *
	 * @param unknown_type $root
	 * @param unknown_type $mainConfigFilename
	 * @param array $pathsMap
	 * @throws Miao_Path_Exception
	 * @throws Miao_Path_Exception_EmptyMap
	 */
	public function __construct( $root, $mainConfigFilename, array $pathsMap )
	{
		$this->_root = $root;

		if ( !file_exists( $mainConfigFilename ) || !is_file( $mainConfigFilename ) || !is_readable( $mainConfigFilename ) )
		{
			$message = sprintf( 'File not found or is not readable (%s)', $mainConfigFilename );
			throw new Miao_Path_Exception( $message );
		}
		$this->_mainConfigFilename = $mainConfigFilename;

		if ( empty( $pathsMap ) )
		{
			throw new Miao_Path_Exception_EmptyMap( 'Map array is required' );
		}
		$this->_map = $pathsMap;
		$this->_pathNameObj = new Miao_Autoload_Name();
	}

	/**
	 *
	 * @return the $_mainConfigFilename
	 */
	public function getMainConfigFilename()
	{
		return $this->_mainConfigFilename;
	}

	public function getRoot()
	{
		return $this->_root;
	}

	public function getRootByLibName( $libName )
	{
		$result = null;
		foreach ( $this->_map as $key => $val )
		{
			if ( 0 === strcasecmp( $key, $libName ) )
			{
				$result = $val;
				break;
			}
		}
		if ( is_null( $result ) )
		{
			throw new Miao_Path_Exception_LibNotFound( $libName );
		}
		return $result;
	}

	/**
	 * Return absolute path for module by class name.
	 *
	 * @param $className string
	 */
	public function getModuleRoot( $className )
	{
		$this->_pathNameObj->parse( $className );
		$libName = $this->_pathNameObj->getLib();
		$libRoot = $this->getRootByLibName( $libName );
		$result = sprintf( '%s/modules/%s', $libRoot, $this->_pathNameObj->getModule() );
		return $result;
	}

	public function getFilenameByClassName( $className )
	{
		$this->_pathNameObj->parse( $className );

		$result = '';
		if ( Miao_Autoload_Name::T_MODULE === $this->_pathNameObj->getType() )
		{
			$dir = $this->getModuleRoot( $className );
			$result = $dir . '/classes/' . $this->_pathNameObj->getModule() . '.class.php';
		}
		else if ( Miao_Autoload_Name::T_CLASS === $this->_pathNameObj->getType() )
		{
			$dir = $this->getModuleRoot( $className );
			$result = $dir . '/classes/' . str_replace( '_', '/', $this->_pathNameObj->getClass() ) . '.class.php';
		}
		else
		{
			throw new Miao_Autoload_Exception_InvalidClassName( $className, 'incomplete name, more "_"' );
		}
		return $result;
	}

	/**
	 *
	 *
	 * Enter description here ...
	 *
	 * @param string $libName
	 */
	public function getModuleList( $libName )
	{
		$dir = $this->getRootByLibName( $libName ) . '/modules';
		$moduleDirList = glob( $dir . '/*', GLOB_ONLYDIR );

		$result = array();
		foreach ( $moduleDirList as $item )
		{
			$index = basename( $item );
			$result[ $index ] = $item;
		}
		return $result;
	}

	public function getLibList()
	{
		$result = array_keys( $this->_map );
		return $result;
	}

	public function getTemplateDir( $className )
	{
		$this->_pathNameObj->parse( $className );

		$result = '';
		if ( Miao_Autoload_Name::T_MODULE === $this->_pathNameObj->getType() || Miao_Autoload_Name::T_CLASS === $this->_pathNameObj->getType() )
		{
			$libName = $this->_pathNameObj->getLib();
			$moduleName = $this->_pathNameObj->getModule();
			$root = $this->getRootByLibName( $libName );
			$path = sprintf( '%s/modules/%s/templates/%s', $root, $moduleName, str_replace( '_', '/', $this->_pathNameObj->getClass() ) );
			$result = rtrim( $path, '/' );
		}
		else
		{
			throw new Miao_Autoload_Exception_InvalidClassName( $className, 'incomplete name' );
		}
		return $result;
	}
}