<?php
/** 
 * User: vpak
 * Date: 04.09.13
 * Time: 18:01 
 */

namespace Miao\Office\Controller;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    static protected $_testOfficeDir;

    static public function setUpBeforeClass()
    {
        $moduleDir = \Miao\App::getInstance()->getPath()->getModuleDir( __CLASS__ );
        $sourceTestOfficeDir = $moduleDir . '/tests/source/TestOffice';

        self::$_testOfficeDir = dirname( $moduleDir ) . DIRECTORY_SEPARATOR . 'TestOffice';

        if ( file_exists( self::$_testOfficeDir ) )
        {
            \Miao\Path\Helper::removeDir( self::$_testOfficeDir );
        }
        \Miao\Path\Helper::copyr( $sourceTestOfficeDir, self::$_testOfficeDir );
    }

    static public function tearDownAfterClass()
    {
        \Miao\Path\Helper::removeDir( self::$_testOfficeDir );
    }

    /**
     * @dataProvider providerTestGenerateContent
     * @param $layout
     * @param $template
     * @param $expected
     */
    public function testGenerateContent( $layout, $template, $expected )
    {
        $view = new \Miao\TestOffice\View\Main();
        $view->debugMode( true );
        if ( $layout )
        {
            $view->setLayout( $layout );
        }
        if ( $template )
        {
            $view->setTemplateFilename( $template );
        }
        $actual = $view->generateContent();

        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestGenerateContent()
    {
        $data = array();

        $layout = 'index.tpl';
        $data[ ] = array( $layout, 'main.tpl', 'file:layouts/index.tpl file:View/main.tpl' );
        $data[ ] = array( $layout, null, 'file:layouts/index.tpl file:View/main.tpl' );

        return $data;
    }


   public function testGetTemplate()
   {
       $view = new \Miao\TestOffice\View\News\Item();
       $actual = $view->generateContent();
       $expected = 'file:layouts/index.tpl file:View/news_item.tpl';
       $this->assertEquals( $expected, $actual );
   }

   public function testAddBlock()
   {
       $view = new \Miao\TestOffice\View\Article();
       $actual = $view->generateContent();
       $expected = 'file:layouts/index.tpl file:View/article.tpl file:ViewBlock/Menu/bottom.tpl file:ViewBlock/Article/Item/index.tpl file:ViewBlock/Menu/bottom.tpl';
       $this->assertEquals( $expected, $actual );
   }
}