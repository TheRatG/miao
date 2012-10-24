<?php
/**
 * @author vpak
 * @date 2012-10-17 18:29:30
 */
abstract class Miao_Router_Rule_Validator
{
	protected $_id;

	/**
	 *
	 * @param array $config
	 * @return Miao_Router_Rule_Validator
	 */
	static public function factory( array $config )
	{
		assert( array_key_exists( 'id', $config ) );
		assert( array_key_exists( 'type', $config ) );

		$type = $config[ 'type' ];
		if ( Miao_Autoload::getInstance()->getFilenameByClassName( $type ) )
		{
			$className = $type;
		}
		else
		{
			$className = 'Miao_Router_Rule_Validator_' . ucfirst( $type );
		}
        
        try
        {
            $result = new $className( $config );    
        }
        catch (Miao_Autoload_Exception_FileNotFound $e)
        {
            throw new Miao_Router_Rule_Exception(sprintf('Validator %s not found.', $className));
        }
		

		if ( !$result instanceof self )
		{
			$message = sprintf(
				'Validator class (%s) must be extend of Miao_Router_Rule_Validator',
				$className );
			throw new Miao_Router_Rule_Validator_Exception( $message );
		}

		$result->_setId( $config[ 'id' ] );
		return $result;
	}

	abstract public function test( $value );

	public function getId()
	{
		return $this->_id;
	}

	protected function _setId( $id )
	{
		$this->_id = $id;
	}
    
    abstract public function getPattern();
}
