<?php
class Miao_Office_ViewHelper_JsCssList
{

	static public function js( $list )
	{
		$result = '';

		foreach ( $list as $value )
		{
			$link = $value[ 'link' ];
			$attributes = $value[ 'attributes' ];
			$result .= sprintf( "\n\t<script src=\"%s\" %s></script>", $link,
				self::_makeTxtAttributes( $attributes ) );
		}

		return $result;
	}

	static public function css( $list )
	{
		$result = '';

		foreach ( $list as $value )
		{
			$link = $value[ 'link' ];
			$attributes = $value[ 'attributes' ];
			$result .= sprintf(
				"\n\t<link href=\"%s\" rel=\"stylesheet\" %s ></link>", $link,
				self::_makeTxtAttributes( $attributes ) );
		}

		return $result;
	}

	static protected function _makeTxtAttributes( array $attributes )
	{
		$result = array();
		foreach ( $attributes as $key => $value )
		{
			$result[] = sprintf( '%s="%s"', $key, $value );
		}
		$result = implode( ' ', $result );
		return $result;
	}
}