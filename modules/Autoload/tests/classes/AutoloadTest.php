<?php
namespace Miao;

class AutoloadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerTestRegister
     * @param $name
     * @param $plugin
     * @param $libPath
     */
    public function testRegister( $name, $plugin, $libPath )
    {
        $autoload = Autoload::getInstance();
        $autoload->registerItem( $name, $plugin, $libPath );

        $expected = $autoload->getPlugin( $name );
        $actual = 'Miao\\Autoload\\Plugin\\' . $plugin;

        $condition = $expected instanceof $actual;
        $this->assertTrue( $condition );
    }

    /**
     * @return array
     */
    public function providerTestRegister()
    {
        $data = array();

        $name = 'Miao';
        $plugin = 'Standart';
        $libPath = $this->_getLibPath();
        $data[ ] = array( $name, $plugin, $libPath );

        return $data;
    }

    /**
     * @dataProvider providerTestGetFilenameByClassName
     * @param $className
     * @param $actual
     */
    public function testGetFilenameByClassName( $className, $actual )
    {

        $autoload = Autoload::getInstance();
        $autoload->registerItem( 'Miao', 'Standart', $this->_getLibPath() );

        $expected = $autoload->getFilenameByClassName( $className );

        $this->assertEquals( $expected, $actual );
    }

    public function providerTestGetFilenameByClassName()
    {
        $data = array();

        $data[ ] = array(
            'Miao\\Autoload\\Plugin',
            $this->_getLibPath() . '/modules/Autoload/classes/Plugin.php'
        );

        $data[ ] = array(
            'Miao\\Autoload\\AutoloadTest',
            $this->_getLibPath() . '/modules/Autoload/tests/classes/AutoloadTest.php'
        );

        $data[ ] = array( 'Miao\\Autoload\\UnknownClass', '' );

        return $data;
    }

    /**
     * @dataProvider providerTestAutoload
     * @param $className
     * @param string $exceptionName
     */
    public function testAutoload( $className, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }

        $autoload = Autoload::getInstance();
        $autoload->registerItem( 'Miao', 'Standart', $this->_getLibPath() );

        $obj = new $className();
    }

    /**
     * @codeCoverageIgnore
     */
    public function providerTestAutoload()
    {
        $data = array();

        $data[ ] = array( 'Miao\\Autoload\\Exception' );

        $exceptionName = 'Miao\\Autoload\\Exception\\FileNotFound';
        $data[ ] = array( 'Miao\\Autoload\\UnknownClass', $exceptionName );
        $data[ ] = array( 'UnknownClass', $exceptionName );

        return $data;
    }

    /**
     * @dataProvider providerTestClassExists
     * @param $className
     * @param $actual
     */
    public function testClassExists( $className, $actual )
    {
        $autoload = Autoload::getInstance();
        $autoload->registerItem( 'Miao', 'Standart', $this->_getLibPath() );

        $expected = $autoload->classExists( $className );
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     */
    public function providerTestClassExists()
    {
        $data = array();

        $data[ ] = array( 'Miao\\Autoload\\ClassInfo', true );
        $data[ ] = array( 'Miao\\Autoload', true );
        $data[ ] = array( 'Miao\\Autoload\\UnknownClass', false );

        return $data;
    }

    protected function _getLibPath()
    {
        $result = realpath( __DIR__ . '/../../../../' );
        return $result;
    }
}