<?php
/**
 * Класс для вставки баннерных инклюдов
 * @author gfilippov
 * @copyright Rbc 2010
 */
class Miao_Office_ViewHelper_Banner extends Miao_Office_ViewHelper
{
	protected $_banners = array();

	public function setBanners( array $ar )
	{
		$this->_banners = $ar;
	}

	public function getBannerCode( $bannerName )
	{
		$code = '';
		if ( !empty( $bannerName ) && in_array( $bannerName, $this->_banners ) )
		{
			$code = sprintf( '<!--#include virtual="/bannermap/%s" -->',
				$bannerName );
		}
		return $code;
	}

	public function getBannerHtml( $bannerName, $startTag = '<div class="banner">', $endTag = '</div>' )
	{
		$code = $this->getBannerCode( $bannerName );
		if ( !empty( $code ) )
		{
			$code = $startTag . $code . $endTag;

			$code = sprintf(
				"<!-- begin banner: %s -->%s<!-- end banner: %s -->",
				$bannerName, $code, $bannerName );
		}
		return $code;
	}

	/**
	 *
	 * @return Miao_Office_ViewHelper_Banner
	 */
	public static function getInstance( $className = '' )
	{
		if ( empty( $className ) )
		{
			$className = __CLASS__;
		}
		$result = parent::_getInstance( $className );
		return $result;
	}

	protected function _initialize()
	{

	}
}