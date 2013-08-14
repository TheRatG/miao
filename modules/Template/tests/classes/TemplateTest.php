<?php
/**
 * @author vpak
 * @date 2013-08-14 10:05:02
 */

namespace Miao\Template;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function testConstruct()
    {
        $templatesDir = \Miao\Application::getInstance()
            ->getPath()
            ->getTestSourcesDir( __CLASS__ );
        $debugMode = false;
        $log = null;
        $tmp = new \Miao\Template( $templatesDir, $debugMode, $log );

        $this->assertEquals( $templatesDir, $tmp->getTemplatesDir() );
        $this->assertEquals( $debugMode, $tmp->debugMode() );

        $exceptionName = '\Miao\Template\Exception';
        $this->setExpectedException( $exceptionName );
        new \Miao\Template( '', $debugMode, $log );
    }

    /**
     * @dataProvider providerTestFetch
     * @param $templateName
     * @param $expected
     * @param string $exceptionName
     */
    public function testFetch( $templateName, $expected, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }

        $templatesDir = \Miao\Application::getInstance()
            ->getPath()
            ->getTestSourcesDir( __METHOD__ );
        $native = new \Miao\Template( $templatesDir, false );

        $actual = $native->fetch( $templateName );
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestFetch()
    {
        $data = array();

        $data[ ] = array( '1.tpl', 'one' );
        $data[ ] = array( '2.tpl', 'one two' );

        return $data;
    }
}