<?php
abstract class Miao_Office_DataHelper_JsCssList extends Miao_Office_DataHelper
{
	const TYPE_JS = 'js';
	const TYPE_CSS = 'css';
	const SECTION_COMMON = 'common';

	protected $_resourceList = array();

	protected $_minify = false;
	protected $_dstFolder = false;
	protected $_client = null;

	/**
	 *
	 * @var Miao_Office_DataHelper_Url
	 */
	protected $_dataHelperUrl;

	protected function _makeLinks( array $fileList )
	{
		$result = array();
		$search = $this->_dstFolder . DIRECTORY_SEPARATOR;
		$replace = '';
		foreach ( $fileList as $value )
		{
			$query = 't=' . Miao_Config::Main()->get( 'timestamp' );
			$result[] = $this->_dataHelperUrl->src( str_replace( $search, $replace, $value ), $query );
		}
		return $result;
	}

	protected function __construct( Miao_Office_DataHelper_Url $dhUrl, $dstFolder, $minify = true )
	{
		$this->_dataHelperUrl = $dhUrl;
		$this->_dstFolder = $dstFolder;
		$this->_minify = $minify;

		$this->_client = new Miaox_Compress_Client( $this->_dstFolder, $this->_minify );
		$this->_init();
	}

	/**
	 * initialize js, css resource
	 */
	abstract protected function _init();

	protected function _addResource( $path, $type, $section = self::SECTION_COMMON )
	{
		assert( !empty( $section ) );
		$this->_prepareType( $type );

		if ( !array_key_exists( $type, $this->_resourceList ) )
		{
			$this->_resourceList[ $type ] = array();
		}
		$typeAr = & $this->_resourceList[ $type ];
		if ( !array_key_exists( $section, $typeAr ) )
		{
			$typeAr[ $section ] = array();
		}
		$sectionAr = &$typeAr[ $section ];
		if ( array_search( $path, $sectionAr ) )
		{
			$msg = sprintf( 'Resource (%s) already exists', $path );
			throw new Miao_Office_DataHelper_JsCssList_Exception( $msg );
		}
		else
		{
			$sectionAr[] = $path;
		}
	}

	public function getResourceList( $type, $section = self::SECTION_COMMON )
	{
		assert( !empty( $section ) );
		$this->_prepareType( $type );

		$result = array();
		if ( isset( $this->_resourceList[ $type ][ $section ] ) )
		{
			$result = $this->_resourceList[ $type ][ $section ];
		}
		return $result;
	}

	protected function _prepareType( $type )
	{
		$type = strtolower( $type );
		if ( $type != self::TYPE_CSS && $type != self::TYPE_JS )
		{
			throw new Miao_Office_DataHelper_JsCssList_Exception( 'Wrong resource type' );
		}
		return $type;
	}
}