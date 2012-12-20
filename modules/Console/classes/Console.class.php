<?php

class Miao_Console
{
	/**
	 * @var string
	 */
	protected $_className;
	/**
	 * @var string
	 */
	protected $_modulePath;
	/**
	 * @var string
	 */
	protected $_filePath;
	/**
	 * @var null|array
	 */
	protected $_parts;
	/**
	 * @var string
	 */
	protected $_author;
	/**
	 * @var Miao_Log
	 */
	protected $_miaoLog;
	/**
	 * @var bool
	 */
	protected $_createTemplate = null;

	/**
	 * @param string $className
	 * @param string $author
	 * @param Miao_Log|null $log
	 * @throws Miao_Console_Exception
	 * @throws Miao_Console_Exception_LibNotFound
	 */
	public function __construct( $className, $author, Miao_Log $log = null )
	{
		$miaoPath = Miao_Path::getInstance();
		$this->_className = $className;
		$this->_author = $author;

		try
		{
			$this->_modulePath = $miaoPath->getModuleRoot( $className );
			$this->_filePath = $miaoPath->getFilenameByClassName( $className );
		}
		catch ( Miao_Path_Exception_LibNotFound $ex )
		{
			throw new Miao_Console_Exception_LibNotFound( $ex->getMessage() );
		}
		catch ( Exception $ex )
		{
			throw new Miao_Console_Exception( $ex->getMessage() );
		}
		$this->_parts = $this->_parse( $className );
		$this->createTemplate();

		$this->_miaoLog = $log;
		$this->_log( 'Classname: ' . $this->_className, Miao_Log::INFO );
		$this->_log( 'Author: ' . $this->_author, Miao_Log::INFO );
		$this->_log( 'Module path: ' . $this->_modulePath, Miao_Log::INFO );
		$this->_log( 'File path: ' . $this->_filePath, Miao_Log::INFO );
		$this->_log( 'Parse result: ' . print_r( $this->_parts, true ), Miao_Log::INFO );
	}

	/**
	 * Создавать шаблоны для View, ViewBlock?
	 * Если в качестве аргумента передан null, то анализируется имя либы
	 * на вхождение "Office"
	 * @param bool|null $val
	 */
	public function createTemplate( $val = null )
	{
		if ( is_null( $val ) )
		{
			if ( false !== stripos( $this->_parts[ 'module' ], 'office' ) )
			{
				$val = true;
			}
		}
		$val = is_bool( $val ) ? $val : false;

		$this->_createTemplate = $val;
	}

	/**
	 * * Добавление нового модуля/класса
	 * @return bool
	 * @throws Miao_Console_Exception
	 */
	public function add()
	{
		$this->_log( 'Call ' . __METHOD__, Miao_Log::DEBUG );

		if ( !empty( $this->_parts[ 'class' ] ) )
		{
			return $this->_addClass( $this->_createTemplate );
		}
		elseif ( !empty( $this->_parts[ 'module' ] ) )
		{
			return $this->_addModule( $this->_createTemplate );
		}
		else
		{
			throw new Miao_Console_Exception( 'Something strange happened.' );
		}
	}


	/**
	 * Создание копии модуля/класса
	 * @param $newClassName
	 * @return bool|int
	 * @throws Miao_Console_Exception
	 * @throws Miao_Console_Exception_DifferentLevels
	 * @throws Miao_Console_Exception_LibNotFound
	 */
	public function cp( $newClassName )
	{
		$this->_log( 'Call ' . __METHOD__, Miao_Log::DEBUG );

		$miaoPath = Miao_Path::getInstance();
		try
		{
			$newModulePath = $miaoPath->getModuleRoot( $newClassName );
			$newFilePath = $miaoPath->getFilenameByClassName( $newClassName );
		}
		catch ( Miao_Path_Exception_LibNotFound $ex )
		{
			throw new Miao_Console_Exception_LibNotFound( $ex->getMessage() );
		}
		catch ( Exception $ex )
		{
			throw new Miao_Console_Exception( $ex->getMessage() );
		}
		$newParts = $this->_parse( $newClassName );

		$this->_log( 'New classname: ' . $newClassName, Miao_Log::INFO );
		$this->_log( 'New module path: ' . $newModulePath, Miao_Log::INFO );
		$this->_log( 'New file path: ' . $newFilePath, Miao_Log::INFO );
		$this->_log( 'New parse result: ' . print_r( $newParts, true ), Miao_Log::INFO );

		$countPartsOfClassName = count( explode( '_', $this->_className ) );
		$countPartsOfNewClassName = count( explode( '_', $newClassName ) );
		if ( $countPartsOfClassName != $countPartsOfNewClassName )
		{
			throw new Miao_Console_Exception_DifferentLevels( 'Different levels of classes!' );
		}

		if ( !empty( $this->_parts[ 'class' ] ) && !empty( $newParts[ 'class' ] ) )
		{
			return $this->_cpClass( $newClassName, $newFilePath );
		}
		elseif ( !empty( $this->_parts[ 'module' ] ) && !empty( $newParts[ 'module' ] ) )
		{
			return $this->_cpModule( $newModulePath, $newParts );
		}
		else
		{
			throw new Miao_Console_Exception( 'Something strange happened.' );
		}
	}


	/**
	 * Переименование модуля/класса
	 * @param $newClassName
	 * @return bool
	 */
	public function ren( $newClassName )
	{
		$this->_log( 'Call ' . __METHOD__, Miao_Log::DEBUG );

		$status = $this->cp( $newClassName );
		if ( $status )
		{
			$this->del();
		}
		return false;
	}


	/**
	 * Удаление модуля/класса
	 * @return bool
	 * @throws Miao_Console_Exception
	 */
	public function del()
	{
		$this->_log( 'Call ' . __METHOD__, Miao_Log::DEBUG );

		if ( !empty( $this->_parts[ 'class' ] ) )
		{
			return $this->_delClass();
		}
		elseif ( !empty( $this->_parts[ 'module' ] ) )
		{
			return $this->_delModule();
		}
		else
		{
			throw new Miao_Console_Exception( 'Something strange happened.' );
		}
	}


	/**
	 * Создание класса
	 * @param bool $createTemplate
	 * @throws Miao_Console_Exception_ClassExists
	 * @throws Miao_Console_Exception_ModuleNotFound
	 * @return bool|int
	 */
	protected function _addClass( $createTemplate = false )
	{
		if ( !is_dir( $this->_modulePath ) )
		{
			throw new Miao_Console_Exception_ModuleNotFound( 'First, create module: ' . $this->_parts[ 'lib' ] . '_' . $this->_parts[ 'module' ] );
		}
		if ( file_exists( $this->_filePath ) )
		{
			throw new Miao_Console_Exception_ClassExists( 'Class ' . $this->_className . ' exists in ' . $this->_filePath );
		}

		if ( $createTemplate && $this->_parts[ 'office' ] )
		{
			$templatePath = $this->_getTemplatePath( $this->_className );
			$content = $this->_getTemplateContent( $this->_className, true );
			$status = Miao_Console_Helper::mkFile( $templatePath, $content );
			$this->_log( 'Create template: ' . $templatePath, ( $status ? Miao_Log::DEBUG : Miao_Log::WARN ) );
		}
		$content = $this->_getTemplateContent( $this->_className );
		$status = Miao_Console_Helper::mkFile( $this->_filePath, $content );
		$this->_log( 'Create class: ' . $this->_filePath, ( $status ? Miao_Log::DEBUG : Miao_Log::WARN ) );

		return $status;
	}

	/**
	 * Создание модуля
	 * @param bool $createTemplate
	 * @return bool
	 * @throws Miao_Console_Exception_ModuleExists
	 */
	protected function _addModule( $createTemplate = false )
	{
		if ( is_dir( $this->_modulePath ) )
		{
			throw new Miao_Console_Exception_ModuleExists( 'Module exists: ' . $this->_parts[ 'module' ] );
		}

		$stdFolders = array( '%s', '%s/data', '%s/classes', '%s/tests/classes', '%s/tests/sources' );
		if ( $createTemplate )
		{
			$stdFolders[] = '%s/templates';
		}
		for ( $i = 0, $c = count( $stdFolders ); $i < $c; $i++ )
		{
			$path = sprintf( $stdFolders[ $i ], $this->_modulePath );
			$status = Miao_Console_Helper::mkDir( $path );
			$this->_log( 'Create dir: ' . $path, ( $status ? Miao_Log::DEBUG : Miao_Log::WARN ) );
		}

		if ( $createTemplate )
		{
			$stdClasses = array( '%s_View', '%s_ViewBlock', '%s_Action' );
			for ( $i = 0, $c = count( $stdClasses ); $i < $c; $i++ )
			{
				$className = sprintf( $stdClasses[ $i ], $this->_className );
				$path = Miao_Path::getInstance()->getFilenameByClassName( $className );
				$content = $this->_getTemplateContent( $className );
				$status = Miao_Console_Helper::mkFile( $path, $content );
				$this->_log( 'Create class: ' . $path, ( $status ? Miao_Log::DEBUG : Miao_Log::WARN ) );
			}
		}
		return true;
	}

	/**
	 * Копия класса
	 * @param string $newClassName
	 * @param string $newFilePath
	 * @return bool|int
	 * @throws Miao_Console_Exception_ClassExists
	 * @throws Miao_Console_Exception_ClassNotFound
	 */
	protected function _cpClass( $newClassName, $newFilePath )
	{
		if ( !file_exists( $this->_filePath ) )
		{
			throw new Miao_Console_Exception_ClassNotFound( 'Class "' . $this->_className . '" not found! ' . $this->_filePath );
		}
		if ( file_exists( $newFilePath ) )
		{
			throw new Miao_Console_Exception_ClassExists( 'Class ' . $newClassName . ' exists! Enter new name.' );
		}

		$oldTemplatePath = $this->_getTemplatePath( $this->_className );
		if ( file_exists( $oldTemplatePath ) )
		{
			$newTemplatePath = $this->_getTemplatePath( $newClassName );
			$content = $this->_getUpdatedContent( $oldTemplatePath, $this->_className, $newClassName );
			$this->_log( 'Copy template: ' . $newTemplatePath, Miao_Log::DEBUG );
			Miao_Console_Helper::mkFile( $newTemplatePath, $content );
		}

		$content = $this->_getUpdatedContent( $this->_filePath, $this->_className, $newClassName );
		$status = Miao_Console_Helper::mkFile( $newFilePath, $content );
		$this->_log( 'Create class: ' . $newFilePath, ( $status ? Miao_Log::DEBUG : Miao_Log::WARN ) );

		return $status;
	}

	/**
	 * Копия модуля
	 * @param string $newModulePath
	 * @param array $newParts
	 * @return bool
	 * @throws Miao_Console_Exception_ModuleNotFound
	 * @throws Miao_Console_Exception_ModuleExists
	 */
	protected function _cpModule( $newModulePath, array $newParts )
	{

		if ( !is_dir( $this->_modulePath ) )
		{
			throw new Miao_Console_Exception_ModuleNotFound( 'Module "' . $this->_parts[ 'lib' ] . '_' . $this->_parts[ 'module' ] . '" not found' );
		}
		if ( is_dir( $newModulePath ) )
		{
			throw new Miao_Console_Exception_ModuleExists( 'Directory for module ' . $newModulePath . ' exists! Enter new name.' );
		}

		$status = true;
		$copyStatus = Miao_Console_Helper::copyDir( $this->_modulePath, $newModulePath );
		$this->_log( 'Copy dir ' . $this->_modulePath . ' to ' . $newModulePath, ( $copyStatus ? Miao_Log::DEBUG : Miao_Log::WARN ) );
		if ( $copyStatus )
		{
			$oldMainClassPath = sprintf( '%s/classes/%s.class.php', $newModulePath, $this->_parts[ 'module' ] );
			$newMainClassPath = sprintf( '%s/classes/%s.class.php', $newModulePath, $newParts[ 'module' ] );
			if ( file_exists( $oldMainClassPath ) && !file_exists( $newMainClassPath ) )
			{
				$status1 = rename( $oldMainClassPath, $newMainClassPath );
				if ( !$status1 )
				{
					$status = false;
				}
			}
			$this->_log( 'Copy main class ' . $oldMainClassPath . ' to ' . $newMainClassPath, ( $status ? Miao_Log::DEBUG : Miao_Log::WARN ) );

			if ( $status )
			{
				$oldModuleClassName = sprintf( '%s_%s', $this->_parts[ 'lib' ], $this->_parts[ 'module' ] );
				$newModuleClassName = sprintf( '%s_%s', $newParts[ 'lib' ], $newParts[ 'module' ] );
				$this->_updateClassNames( $newModulePath, $oldModuleClassName, $newModuleClassName );
			}
		}

		return $status;
	}

	/**
	 * Удаление класса
	 * @return bool
	 * @throws Miao_Console_Exception_ClassNotFound
	 */
	protected function _delClass()
	{
		if ( !file_exists( $this->_filePath ) )
		{
			throw new Miao_Console_Exception_ClassNotFound( 'Class "' . $this->_className . '" not found! ' . $this->_filePath );
		}

		if ( $this->_parts[ 'office' ] )
		{
			$templatePath = $this->_getTemplatePath( $this->_className );
			$status = Miao_Console_Helper::delFile( $templatePath );
			$this->_log( 'Delete template ' . $templatePath, ( $status ? Miao_Log::DEBUG : Miao_Log::WARN ) );
		}
		$status = Miao_Console_Helper::delFile( $this->_filePath );
		$this->_log( 'Delete class ' . $this->_filePath, ( $status ? Miao_Log::DEBUG : Miao_Log::WARN ) );

		return $status;
	}

	/**
	 * Удаление модуля
	 * @return bool
	 * @throws Miao_Console_Exception_ModuleNotFound
	 */
	protected function _delModule()
	{
		if ( !is_dir( $this->_modulePath ) )
		{
			throw new Miao_Console_Exception_ModuleNotFound( 'Module "' . $this->_parts[ 'module' ] . '" not found' );
		}

		$this->_log( 'Delete module dir: ' . $this->_modulePath, Miao_Log::DEBUG );
		$status = Miao_Console_Helper::delDir( $this->_modulePath );

		return $status;
	}


	/**
	 * Замена имен старых классов новыми
	 * @param $path
	 * @param $oldClassName
	 * @param $newClassName
	 */
	protected function _updateClassNames( $path, $oldClassName, $newClassName )
	{
		$this->_log( 'Update class names from ' . $oldClassName . ' to ' . $newClassName . ' in folder ' . $path, Miao_Log::DEBUG );
		$files = Miao_Console_Helper::fileList( $path . '/{*.class.php,*.class.Test.php,*.tpl}', GLOB_BRACE );
		foreach ( $files as $filename )
		{
			$content = $this->_getUpdatedContent( $filename, $oldClassName, $newClassName );
			$status = file_put_contents( $filename, $content );
			$this->_log( 'Replace ' . $oldClassName . ' to ' . $newClassName . ' in file ' . $filename, ( $status ? Miao_Log::DEBUG : Miao_Log::WARN ) );
		}
	}


	/**
	 * Возвращает обновленное содержимое файла
	 * @param $path
	 * @param $oldClassName
	 * @param $newClassName
	 * @return mixed|string
	 */
	protected function _getUpdatedContent( $path, $oldClassName, $newClassName )
	{
		$content = file_get_contents( $path );
		if ( !$content )
		{
			$this->_log( 'Can\'t read file ' . $path, Miao_Log::WARN );
		}
		else
		{
			$content = str_replace( $oldClassName, $newClassName, $content );
		}

		return $content;
	}


	/**
	 * Содержимое файлов
	 * @param $className
	 * @param bool $isTemplate
	 * @internal param $dst
	 * @return bool
	 */
	protected function _getTemplateContent( $className, $isTemplate = false )
	{
		$parse = $this->_parse( $className );
		$parse[ 'office' ] = false;
		$template = 'standart';
		$ext = !$isTemplate ? 'class' : 'tpl';
		$parentClassName = '';

		if ( !empty( $parse[ 'class' ] ) )
		{
			if ( $this->_createTemplate )
			{
				$parts = explode( '_', $parse[ 'class' ] );
				if ( false !== in_array( $parts[ 0 ], array( 'View', 'ViewBlock', 'Action' ) ) )
				{
					$parse[ 'office' ] = true;
					$template = strtolower( $parts[ 0 ] );

					if ( !$isTemplate )
					{
						$parentClassName = sprintf( 'Miao_Office_%s', $parts[ 0 ] );
						if ( count( $parts ) > 1 )
						{
							$parentClassName = sprintf( '%s_%s_%s', $parse[ 'lib' ], $parse[ 'module' ], $parts[ 0 ] );
							$template .= '_child';
						}
					}
				}
			}
		}

		$fileName = sprintf( '%s/data/templates/%s.%s', Miao_Path::getInstance()->getModuleRoot( __CLASS__ ), $template, $ext );
		$content = file_get_contents( $fileName );
		$content = str_replace(
			array( '%CLASS%', '%LOGIN%', '%DATE%', '%PARENT_CLASS%' )
			, array( $className, $this->_author, date( 'Y-m-d H:i:s' ), $parentClassName )
			, $content
		);

		return $content;
	}


	/**
	 * Путь к шаблону
	 * @param $className
	 * @return string
	 */
	protected function _getTemplatePath( $className )
	{
		$parts = explode( '_', $className );
		if ( $parts[ 2 ] != 'View' )
		{
			$filename = ucfirst( array_pop( $parts ) ) . '/index.tpl';
			$dir = Miao_Path::getInstance()->getTemplateDir( implode( '_', $parts ) );
		}
		else
		{
			$filename = strtolower( array_pop( $parts ) ) . '.tpl';
			$forDir = array_slice( $parts, 0, 3 );
			$dir = Miao_Path::getInstance()->getTemplateDir( implode( '_', $forDir ) );
			$forFile = array_slice( $parts, 3 );
			$prefixFilename = strtolower( implode( '_', $forFile ) );
			if ( !empty( $prefixFilename ) )
			{
				$prefixFilename .= '_';
			}
			$filename = $prefixFilename . $filename;
		}
		if ( !empty( $dir ) )
		{
			$dir .= DIRECTORY_SEPARATOR;
		}
		return $dir . $filename;
	}

	/**
	 * Разбиваем имя класса на составляющие
	 * @param $className
	 * @throws Miao_Console_Exception_WrongLibType
	 * @return array
	 */
	protected function _parse( $className )
	{
		$result = array( 'type' => '', 'name' => '', 'lib' => '', 'module' => '', 'class' => '', 'office' => false );
		try
		{
			$parser = new Miao_Autoload_Name();
			$parser->parse( $className );
			$result = $parser->toArray();
		}
		catch ( Miao_Autoload_Exception $ex )
		{
			$this->_log( 'Error parse classname ' . $className, Miao_Log::WARN );
		}

		$autoLoad = Miao_Autoload::getInstance()->getRegisterList();
		$libName = strtolower( $result[ 'lib' ] );
		if ( !isset( $autoLoad[ $libName ] ) || !( $autoLoad[ $libName ] instanceof Miao_Autoload_Plugin_Standart ) )
		{
			throw new Miao_Console_Exception_WrongLibType( 'Type of autoloader lib must be "standart"!' );
		}

		// Обработка Office
		if ( !empty( $result[ 'class' ] ) )
		{
			$parts = explode( '_', $result[ 'class' ] );
			if ( false !== in_array( $parts[ 0 ], array( 'View', 'ViewBlock' ) ) )
			{
				$result[ 'office' ] = true;
			}
		}

		return $result;
	}


	/**
	 * Обертка для Miao_Log
	 * @param $str
	 * @param int $priority
	 */
	private function _log( $str, $priority = Miao_Log::DEBUG )
	{
		if ( !is_null( $this->_miaoLog ) )
		{
			$this->_miaoLog->log( $str, $priority );
		}
	}
}