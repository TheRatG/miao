<?php
namespace Miao\Autoload;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    public function testIncludePath()
    {
        $includePath = get_include_path();
        $addPath = dirname( __DIR__ );

        $actual = $includePath . PATH_SEPARATOR . $addPath;
        $expected = Plugin::addIncludePath( $addPath );
        $this->assertEquals( $expected, $actual );

        set_include_path( $includePath );

        $actual = $addPath . PATH_SEPARATOR . $includePath;
        $expected = Plugin::addIncludePath( $addPath, 0 );
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @dataProvider providerTestGetFilenameByClassName
     * @param $className
     * @param $expected
     */
    public function testGetFilenameByClassName( $className, $expected )
    {
        $libName = 'Miao';
        $path = \Miao\App::getInstance()
            ->getPath();

        $plugin = new \Miao\Autoload\Plugin\Standart( $libName, $path->getRootDir( $libName ) );
        $actual = $plugin->getFilenameByClassName( $className );

        $expected = $path->getRootDir( $libName ) . DIRECTORY_SEPARATOR . $expected;

        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestGetFilenameByClassName()
    {
        $data = array();

        $data[ ] = array( 'Miao_Office', 'modules/Office/classes/Office.php' );
        $data[ ] = array( 'Miao_TestOffice', 'modules/TestOffice/classes/TestOffice.php' );
        $data[ ] = array( 'Miao_Office_View', 'modules/Office/classes/View.php' );
        $data[ ] = array( 'Miao_TestOfficeTest', 'modules/TestOffice/tests/classes/TestOfficeTest.php' );

        return $data;
    }
}