<?php
/**
 * @author vpak
 * @date 2013-08-14 10:05:02
 */

namespace Miao\Template;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    protected $_templatesDir;

    protected $_templateName = 'template.tpl';

    public function setUp()
    {
        $this->_templatesDir = $templatesDir = \Miao\App::getInstance()
            ->getPath()
            ->getTestSourcesDir( __METHOD__ );
    }

    public function tearDown()
    {
        $tplFilename = $this->_templatesDir . DIRECTORY_SEPARATOR . $this->_templateName;
        if ( file_exists( $tplFilename ) )
        {
            unlink( $tplFilename );
        }
    }

    public function testConstruct()
    {
        $templatesDir = \Miao\App::getInstance()
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

        $templatesDir = \Miao\App::getInstance()
            ->getPath()
            ->getTestSourcesDir( __METHOD__ );
        $native = new \Miao\Template( $templatesDir, false );
        $native->setConsumeException( false );

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
        $data[ ] = array( 'fileNotFound.tpl', '', '\Miao\Template\Exception\OnFileNotFound' );

        return $data;
    }

    /**
     * @dataProvider providerTestIncludeTemplate
     * @param $templateName
     * @param $expected
     * @param string $exceptionName
     */
    public function testIncludeTemplate( $templateName, $expected, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }

        $templatesDir = \Miao\App::getInstance()
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
    public function providerTestIncludeTemplate()
    {
        $data = array();

        $data[ ] = array( '1.tpl', 'one 1sub' );
        $data[ ] = array( '2.tpl', 'two 2_2 plus  other' );

        return $data;
    }

    /**
     * @dataProvider data4SetValueOf
     * @param $name
     * @param $value
     */
    public function testSetValueOf( $name, $value )
    {
        $this->_generateTemplate();
        $pn = new \Miao\Template( $this->_templatesDir );

        $pn->setValueOf( $name, $value );
        $res2 = $pn->fetch( $this->_templateName );
        $this->assertEquals( $res2, $value );
    }

    public function data4SetValueOf()
    {
        return array(
            array( 'val_name', 59 ),
            array( 'val_name', '756' ),
            array( 'val_name', 'текст строки' ),
            array( 'val_name', 'бяка' ) );
    }

    public function testResetTemplateVariables()
    {
        $pn = new \Miao\Template( $this->_templatesDir, false );

        $pn->setValueOf( 'name1', 'Начало' );
        $pn->setValueOf( 'name2', ' середина' );
        $pn->setValueOf( 'name3', ' середина2' );
        $pn->setValueOf( 'name4', ' конец.' );

        $this->_generateTemplate( 'name1', array( 'name2', 'name3', 'name4' ) );
        $res = $pn->fetch( $this->_templateName );
        $this->assertEquals( $res, 'Начало середина середина2 конец.' );

        $pn->resetTemplateVariables();
        $res = $pn->fetch( $this->_templateName );
        $this->assertEquals( $res, '' );
    }

    protected function _generateTemplate( $valName = 'val_name', $additionalVars = false )
    {
        $tplFilename = $this->_templatesDir . DIRECTORY_SEPARATOR . $this->_templateName;
        if ( file_exists( $tplFilename ) )
        {
            unlink( $tplFilename );
        }
        $s = '<?=$this->getValueOf(\'' . $valName . '\' )?>';
        if ( is_array( $additionalVars ) && count( $additionalVars ) > 0 )
        {
            foreach ( $additionalVars as $v )
            {
                $s .= '<?=$this->getValueOf(\'' . $v . '\' )?>';
            }
        }
        file_put_contents( $tplFilename, $s );
    }
}