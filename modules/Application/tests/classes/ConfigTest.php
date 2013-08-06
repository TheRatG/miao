<?php
namespace Miao\Application;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Miao\Application\Config
     */
    public function getConfig()
    {
        $config = new \Miao\Application\Config(
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