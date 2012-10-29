<?php
/**
 * @author vpak
 * @date 2012-10-18 10:38:51
 */
class Miao_Router_Rule_Validator_Regexp extends Miao_Router_Rule_Validator
{
	/**
	 *
	 * @var string
	 */
	private $_pattern;
	private $_slash = 0;

	/**
	 *
	 * @param array $config
	 * @throws Miao_Router_Rule_Validator_Exception
	 */
	public function __construct( array $config )
	{
		if ( !isset( $config[ 'pattern' ] ) )
		{
			throw new Miao_Router_Rule_Validator_Exception( 'Invalid config: param "pattern" was not found' );
		}
		$this->_pattern = $config[ 'pattern' ];
		if ( array_key_exists( 'slash', $config ) )
		{
			$this->_slash = $config[ 'slash' ];
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see Miao_Router_Rule_Validator::test()
	 */
	public function test( $value )
	{
		// preg_quote($keywords, '/'); не сработало
		$pt = '/' . str_replace( '/', '\/', $this->_pattern ) . '/';
		$result = preg_match( $pt, $value );
		return $result;
	}

	/**
	 *
	 * @return string
	 */
	public function getPattern()
	{
		return $this->_pattern;
	}

	public function getSlash()
	{
		return $this->_slash;
	}
}
