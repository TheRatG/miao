<?php
namespace Miao\App;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Miao\App\Config
     */
    public function getConfig()
    {
        $config = new \Miao\App\Config(
            array(), array( 'Miaox' => array( 'Sphinx' => array( 'host' => 'localhost' ) ) ), 'test'
        );
        return $config;
    }

    public function testGetConfig()
    {
        $config = $this->getConfig();
        $actual = 'localhost';
        $expected = $config
            ->getObject( '\Miaox\Sphinx' )
            ->get( 'host' );
        $this->assertEquals( $expected, $actual );

        $expected = $config->getObject( '\Miaox\Office', false );
        $actual = null;
        $this->assertEquals( $expected, $actual );

        $this->setExpectedException( '\Miao\Config\Exception' );
        $config
            ->getObject( '\Miaox\Office' );
    }
}