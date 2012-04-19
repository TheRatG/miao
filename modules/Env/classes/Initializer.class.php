<?php
/**
 * @subpackage Miao_Env
 *
 */
class Miao_Env_Initializer
{
	private $_config = null;

	/**
	 * Enter description here...
	 *
	 * @param Miao_Config_Base $config
	 */
	public function __construct( Miao_Config_Base $config )
	{
		$this->_config = $config;
	}

	/**
	 * Enter description here...
	 *
	 */
	public function run()
	{
		$error_level = $this->_config->get( 'error_level', false );
		if ( is_numeric( $error_level ) )
		{
			$this->setErrorLevel( $error_level );
		}

		$default_timezone = $this->_config->get( 'default_timezone', false );
		if ( $default_timezone )
		{
			$this->setDefaultTimezone( $default_timezone );
		}

		$umask = $this->_config->get( 'umask', false );
		if ( $umask )
		{
			$this->setUmask();
		}

		$unregister_globals = $this->_config->get( 'unregister_globals', false );
		if ( $unregister_globals )
		{
			$this->unregisterGlobals();
		}

		$strip_global_slashes = $this->_config->get( 'strip_global_slashes', false );
		if ( $strip_global_slashes && version_compare( PHP_VERSION, '5.3.0' ) == false )
		{
			$this->stripGlobalSlashes();
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $level
	 * @return unknown
	 */
	public function setErrorLevel( $level = null )
	{
		if ( null !== $level )
		{
			error_reporting( $level );
		}
		else
		{
			error_reporting( E_ALL | E_STRICT );
		}
		return $this;
	}

	/**
	 *
	 * @param unknown_type $timezone
	 * @return unknown
	 */
	public function setDefaultTimezone( $timezone = null )
	{
		// All timezones available in http://unicode.org/cldr/data/diff/supplemental/territory_containment_un_m_49.html
		if ( null === $timezone )
		{
			$timezone = 'GMT';
		}
		date_default_timezone_set( $timezone );
		return $this;
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	public function setUmask()
	{
		umask( 0 );
		return $this;
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	public function unregisterGlobals()
	{
		$rg = ini_get( 'register_globals' );
		if ( $rg === '' || $rg === '0' || strtolower( $rg ) === 'off' )
		{
			return $this;
		}

		// Prevent script.php?GLOBALS[ foo ]=bar
		if ( isset( $_REQUEST[ 'GLOBALS' ] ) || isset( $_FILES[ 'GLOBALS' ] ) )
		{
			exit( 'I\'ll have a steak sandwich and... a steak sandwich.' );
		}

		$noUnset = array(
			'GLOBALS',
			'_GET',
			'_POST',
			'_COOKIE',
			'_REQUEST',
			'_SERVER',
			'_ENV',
			'_FILES' );

		$input = array_merge( $_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset( $_SESSION ) && is_array( $_SESSION ) ? $_SESSION : array() );
		foreach ( $input as $k => $v )
		{
			if ( !in_array( $k, $noUnset ) && isset( $GLOBALS[ $k ] ) )
			{
				unset( $GLOBALS[ $k ] );
				unset( $GLOBALS[ $k ] ); // Double unset to circumvent the zend_hash_del_key_or_index hole in PHP < 5.1.4.
			}
		}

		return $this;
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	public function stripGlobalSlashes()
	{

		set_magic_quotes_runtime( 0 );

		if ( get_magic_quotes_gpc() )
		{
			$_GET = $this->_stripSlashesArray( $_GET );
			$_POST = $this->_stripSlashesArray( $_POST );
			$_COOKIE = $this->_stripSlashesArray( $_COOKIE );
			$_REQUEST = $this->_stripSlashesArray( $_REQUEST );
		}

		return $this;
	}

	/**
	 * Enter description here...
	 *
	 */
	protected function _initSet()
	{
		$upload_tmp_dir = $this->_config->get( 'upload_tmp_dir', false );
		if ( $upload_tmp_dir )
		{
			ini_set( 'upload_tmp_dir', $this->_config->upload_tmp_dir );
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $array
	 * @return unknown
	 */
	protected function _stripSlashesArray( &$array )
	{
		return is_array( $array ) ? array_map( array(
			$this,
			'_stripSlashesArray' ), $array ) : stripslashes( $array );
	}
}
