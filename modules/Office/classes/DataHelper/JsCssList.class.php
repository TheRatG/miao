<?php
abstract class Miao_Office_DataHelper_JsCssList extends Miao_Office_DataHelper
{
	const TYPE_JS = 'js';
	const TYPE_CSS = 'css';
	const SECTION_COMMON = 'common';
	protected $_resourceList = array();
	protected $_minify = false;
	protected $_dstFolder = false;
	protected $_buildTimestamp = false;

	/**
	 *
	 * @var Miao_Office_DataHelper_Url
	 */
	protected $_dataHelperUrl;

	/**
	 * initialize js, css resource
	 */
	abstract protected function _init();

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

	protected function __construct( Miao_Office_DataHelper_Url $dhUrl, $dstFolder )
	{
		$this->_dataHelperUrl = $dhUrl;
		$this->_dstFolder = $dstFolder;
		$this->_buildTimestamp = Miao_Config::Main()->get( 'timestamp' );
		$this->_init();
	}

	protected function _makeLink( $path )
	{
		$search = $this->_dstFolder . DIRECTORY_SEPARATOR;
		$replace = '';
		$query = 't=' . $this->_buildTimestamp;
		$result = $this->_dataHelperUrl->src(
			str_replace( $search, $replace, $path ), $query );
		return $result;
	}

	protected function _addResource( $path, $type, array $attributes = array(), $section = self::SECTION_COMMON )
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
			$tmp = array(
				'path' => $path,
				'attributes' => $attributes,
				'link' => $this->_makeLink( $path ) );
			$sectionAr[] = $tmp;
		}
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