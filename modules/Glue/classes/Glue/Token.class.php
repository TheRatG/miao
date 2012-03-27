<?php
class Miao_Glue_Token
{
	private $_tokens = array();
	private $_brakets = 0;

	static public function factory( $filename )
	{
		$content = file_get_contents( $filename );
		$result = new Miao_Glue_Token( token_get_all( $content ) );
		return $result;
	}

	public function __construct( array $tokens )
	{
		$this->_tokens = $tokens;
		$this->_clearIncludeAndRequire();
	}

	public function toString( $compact = false )
	{
		$pieces = array();
		foreach ( $this->_tokens as $token )
		{
			if ( is_array( $token ) )
			{
				if ( in_array( $token[ 0 ], array( T_OPEN_TAG, T_CLOSE_TAG ) ) )
				{
					continue;
				}
				else if ( true === $compact && in_array( $token[ 0 ], array(
					T_COMMENT,
					T_DOC_COMMENT ) ) )
				{
					continue;
				}
				$pieces[] = $token[ 1 ];
			}
			else
			{
				$pieces[] = $token;
			}
		}

		$result = implode( '', $pieces );
		return $result;
	}

	protected function _clearIncludeAndRequire()
	{
		$flag = false;
		foreach ( $this->_tokens as $key => $token )
		{
			//внутренние include нужны
			if ( '{' === $token )
			{
				$this->_brakets++;
			}
			else if ( '}' === $token )
			{
				$this->_brakets--;
			}

			if ( 0 == $this->_brakets )
			{
				// удалить все что до ; после include
				if ( ';' === $token )
				{
					if ( true === $flag )
					{
						unset( $this->_tokens[ $key ] );
					}
					$flag = false;
				}
				// удалить все include
				if ( true === $flag || in_array( $token[ 0 ], array(
					T_REQUIRE,
					T_REQUIRE_ONCE,
					T_INCLUDE,
					T_INCLUDE_ONCE ) ) )
				{
					$flag = true;
					unset( $this->_tokens[ $key ] );
				}
			}
		}
	}
}