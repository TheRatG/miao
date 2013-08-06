<?php
namespace Miao\Application;

class Config
{
    const MAIN_NAME = 'Main';

    /**
     * @var array \Miao\Config\Base[]
     */
    protected $_config = array();

    /**
     * @param string $name
     * @param bool $throwException
     * @return \Miao\Config\Base|null
     * @throws \Exception
     */
    public function getObject( $name = self::MAIN_NAME, $throwException = true )
    {
        $result = null;
        if ( !$name )
        {
            $name = self::CONFIG_MAIN_NAME;
        }
        $name = ltrim( $name, '\\' );
        $items = explode( '\\', $name );

        $libName = array_shift( $items );
        if ( isset( $this->_config[ $libName ] ) )
        {
            $result = $this->_config[ $libName ];
        }

        if ( count( $items ) > 0 && $result instanceof \Miao\Config\Base )
        {
            try
            {
                $data = $result->get( implode( $items ) );
                $result = new \Miao\Config\Base( $data );
            }
            catch ( \Miao\Config\Exception $e )
            {
                $result = null;
                if ( $throwException )
                {
                    throw $e;
                }
            }
        }
        return $result;
    }

    public function setConfig( $name, array $data )
    {
        $this->_config[ $name ] = new \Miao\Config\Base( $data );
    }

    public function __construct( array $configMain, array $configModules = array() )
    {
        if ( isset( $configMain[ 'config' ] ) )
        {
            $configMain = $configMain[ 'config' ];
        }
        $this->_config[ self::MAIN_NAME ] = new \Miao\Config\Base( $configMain );
        foreach ( $configModules as $name => $data )
        {
            $this->setConfig( $name, $data );
        }
    }
}