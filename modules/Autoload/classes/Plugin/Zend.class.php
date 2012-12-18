<?php
class Miao_Autoload_Plugin_Zend extends Miao_Autoload_Plugin
{
	public function __construct( $name, $libPath )
	{
		parent::__construct( $name, $libPath );
		self::addIncludePath( $libPath );
	}

	public function getFilenameByClassName( $className )
	{
        if (strpos($className, '\\') === false)
        {
            $items = explode( '_', $className );
        }
        else
        {
            $items = explode( '\\', $className );
        }
        if ( count( $items ) < 2 || $items[ 0 ] != $this->getName() )
		{
			return '';
		}
		$result = sprintf( '%s/%s.php', $this->getLibPath(), implode( '/', $items ) );
		return $result;
	}
}