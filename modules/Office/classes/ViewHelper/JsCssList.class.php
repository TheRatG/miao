<?php
class Miao_Office_ViewHelper_JsCssList
{
	static public function js( $list )
	{
		$result = '';

		foreach ( $list as $value )
		{
			$result .= sprintf( "\n\t<script src=\"%s\"></script>", $value );
		}

		return $result;
	}

	static public function css( $list )
	{
		$result = '';

		foreach ( $list as $value )
		{
			$result .= sprintf( "\n\t<link href=\"%s\" rel=\"stylesheet\">", $value );
		}

		return $result;
	}
}