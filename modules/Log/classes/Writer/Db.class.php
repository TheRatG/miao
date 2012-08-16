<?php
class Miao_Log_Writer_Db extends Miao_Log_Writer_Abstract
{
	/**
	 * Database adapter instance
	 * @var Miao_Db_Adapter
	 */
	private $_db;

	/**
	 * Name of the log table in the database
	 * @var string
	 */
	private $_table;

	/**
	 * Relates database columns names to log data field keys.
	 *
	 * @var null|array
	 */
	private $_columnMap;

	/**
	 * Class constructor
	 *
	 * @param Miao_Db_Adapter $db   Database adapter instance
	 * @param string $table         Log table in database
	 * @param array $columnMap
	 */
	public function __construct( $db, $table, $columnMap = null )
	{
		$this->_db = $db;
		$this->_table = $table;
		$this->_columnMap = $columnMap;
	}

	/**
	 * Create a new instance of Miao_Log_Writer_Db
	 *
	 * @param  array|Miao_Config $config
	 * @return Miao_Log_Writer_Db
	 * @throws Miao_Log_Exception
	 */
	static public function factory( $config )
	{
		$config = self::_parseConfig( $config );
		$config = array_merge(
			array( 'db' => null, 'table' => null, 'columnMap' => null ), $config );

		if ( isset( $config[ 'columnmap' ] ) )
		{
			$config[ 'columnMap' ] = $config[ 'columnmap' ];
		}

		return new self( $config[ 'db' ], $config[ 'table' ], $config[ 'columnMap' ] );
	}

	/**
	 * Formatting is not possible on this writer
	 */
	public function setFormatter( Miao_Log_Formatter_Interface $formatter )
	{
		throw new Miao_Log_Exception( get_class( $this ) . ' does not support formatting' );
	}

	/**
	 * Remove reference to database adapter
	 *
	 * @return void
	 */
	public function shutdown()
	{
		$this->_db = null;
	}

	/**
	 * Write a message to the log.
	 *
	 * @param  array  $event  event data
	 * @return void
	 */
	protected function _write( $event )
	{
		if ( $this->_db === null )
		{
			throw new Miao_Log_Exception( 'Database adapter is null' );
		}

		if ( $this->_columnMap === null )
		{
			$dataToInsert = $event;
		}
		else
		{
			$dataToInsert = array();
			foreach ( $this->_columnMap as $columnName => $fieldKey )
			{
				$dataToInsert[ $columnName ] = $event[ $fieldKey ];
			}
		}

		$this->_db->insert( $this->_table, $dataToInsert );
	}
}
