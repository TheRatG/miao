<?php
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Miao\App
     */
    public function getApp()
    {
        $app = \Miao\App::init(
            array( 'project_root' => '', 'libs' => array() ), array(),
            array( 'Miaox' => array( 'Sphinx' => array( 'host' => 'localhost' ) ) ), 'test'
        );
        return $app;
    }

    public function testGetConfig()
    {
        $app = $this->getApp();
        $actual = 'localhost';
        $expected = $app
            ->getConfig( '\\Miaox\\Sphinx' )
            ->get( 'host' );
        $this->assertEquals( $expected, $actual );
    }
}