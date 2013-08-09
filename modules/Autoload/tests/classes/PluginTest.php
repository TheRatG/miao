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
}