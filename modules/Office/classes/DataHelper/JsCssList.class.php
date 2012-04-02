<?php
abstract class Miao_Office_DataHelper_JsCssList
{
	// you can change ------------
	protected $_jsList;
	protected $_cssList;
	protected $_minify = false;
	protected $_dstFolder = false;

	protected $_jsPath = 'jslib';
	protected $_cssPath = 'skin';
	// ---------------------------

	/**
	 * @var Miao_Office_DataHelper_Url
	 */
	protected $_dataHelperUrl;

	// generated -----------------
	protected $_jsSrcList;
	protected $_cssSrcList;

	protected $_jsFilenameList;
	protected $_cssFilenameList;
	// ---------------------------

	/**
	 * @return the $_dataHelperUrlClassName
	 */
	public function getDataHelperUrlClassName()
	{
		return $this->_dataHelperUrlClassName;
	}

	/**
	 * @return the $_jsList
	 */
	public function getJsSrcList()
	{
		$result = $this->_jsSrcList;
		if ( $this->_minify )
		{
			$result  = array( $this->getMinifyJsSrc() );
		}
		return $result;
	}

	/**
	 * @return the $_cssList
	 */
	public function getCssSrcList()
	{
		$result = $this->_cssSrcList;
		if ( $this->_minify )
		{
			$result  = array( $this->getMinifyCssSrc() );
		}

		return $result;
	}

	public function getJsFilenameList()
	{
		return $this->_jsFilenameList;
	}

	public function getCssFilenameList()
	{
		return $this->_cssFilenameList;
	}

	public function __construct()
	{
		$this->_init();
		$this->_initSrcList();
		$this->_initFilenameList();
	}

	public function getJsFolder()
	{
		$result = $this->_getDstFolder() . DIRECTORY_SEPARATOR . $this->_jsPath;
		return $result;
	}

	public function getCssFolder()
	{
		$result = $this->_getDstFolder() . DIRECTORY_SEPARATOR . $this->_cssPath;
		return $result;
	}

	public function getMinifyJsFilename()
	{
		$jsList = $this->getJsFilenameList();
		$dstFolder = $this->getJsFolder();
		$result = Miao_Compress::makeFilename( $dstFolder, $jsList );
		return $result;
	}

	public function getMinifyCssFilename()
	{
		$cssList = $this->getCssFilenameList();
		$dstFolder = $this->getCssFolder();
		$result = Miao_Compress::makeFilename( $dstFolder, $cssList );
		return $result;
	}

	public function getMinifyJsSrc()
	{
		$jsList = $this->getJsFilenameList();
		$path = Miao_Compress::makeFilename( 'jslib', $jsList );
		$result = $this->_src( $path );
		return $result;
	}

	public function getMinifyCssSrc()
	{
		$cssList = $this->getCssFilenameList();
		$path = Miao_Compress::makeFilename( 'skin', $cssList );
		$result = $this->_src( $path );
		return $result;
	}

	static protected function _getInstance( $className )
	{
		$index = 'dh:jscsslist' . $className;
		$result = null;
		if ( !Miao_Registry::isRegistered( $index ) )
		{
			$result = new $className();
			Miao_Registry::set( $index, $result );
		}
		else
		{
			$result = Miao_Registry::get( $index );
		}
		return $result;
	}

	protected function _initSrcList()
	{
		$urlHelper = $this->_dataHelperUrl;
		foreach ( $this->_jsList as $item )
		{
			$this->_jsSrcList[] = $urlHelper->src( $this->_jsPath . '/' . $item );
		}
		foreach ( $this->_cssList as $item )
		{
			$this->_cssSrcList[] = $urlHelper->src(
			$this->_cssPath . '/' . $item );
		}
	}

	protected function _initFilenameList()
	{
		$dir = $this->_getDstFolder();

		foreach ( $this->_jsList as $item )
		{
			$this->_jsFilenameList[] = $dir . '/' . $this->_jsPath . '/' . $item;
		}
		foreach ( $this->_cssList as $item )
		{
			$this->_cssFilenameList[] = $dir . '/' . $this->_cssPath . '/' . $item;
		}
	}

	protected function _getDstFolder()
	{
		$dir = $this->_dstFolder;
		if ( !$dir )
		{
			$path = Miao_Path::getDefaultInstance();
			$dir = $path->getModuleRoot( get_class( $this->_dataHelperUrl ) );
			$dir .= '/public/images';
		}
		return $dir;
	}

	protected function _src( $path )
	{
		$helper = $this->_dataHelperUrl;
		$result = $helper->src( $path );
		return $result;
	}

	abstract protected function _init();
}