<?php
/**
 * @author vpak
 * @date 2013-08-13 15:23:26
 */

namespace Miao\Office;

class HeaderTest extends \PHPUnit_Framework_TestCase
{
    private $_headerObj = null;

    public function setUp()
    {
        $this->_headerObj = new \Miao\Office\Header();
        $this->_headerObj->reset();
    }

    /**
     * @dataProvider providerTestSet
     * @param $name
     * @param $value
     */
    public function testSet( $name, $value )
    {
        $headerObj = $this->_headerObj;
        $headerObj->set( $name, $value );
        $headerList = $headerObj->getList();

        $checkKey = array_key_exists( $name, $headerList );
        $this->assertTrue( $checkKey );
        if ( $checkKey )
        {
            $expected = $headerList[ $name ];
            $this->assertEquals( $expected, $value );
        }
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestSet()
    {
        $data = array();

        $data[ ] = array( 'Content-Disposition', 'inline; filename=result.pdf' );
        $data[ ] = array( 'Content-type', 'application/x-pdf' );

        return $data;
    }

    /**
     * @dataProvider providerTestContentType
     * @param $contentType
     * @param $expected
     * @param string $exceptionName
     */
    public function testContentType( $contentType, $expected, $exceptionName = '' )
    {
        if ( !empty( $exceptionName ) )
        {
            $this->setExpectedException( $exceptionName );
        }

        $headerObj = $this->_headerObj;
        $headerObj->setContentType( $contentType );
        $actual = $headerObj->getContentType();

        $this->assertEquals( $expected, $actual );

        $expected = $headerObj->get( 'Content-type' );
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestContentType()
    {
        $data = array();

        $data[ ] = array( 'text/html', 'Content-type: text/html' );
        $data[ ] = array( 'html', 'Content-type: text/html' );
        $data[ ] = array( 'xml', 'Content-type: application/xml' );
        $data[ ] = array( 'application/xml', 'Content-type: application/xml' );
        $data[ ] = array( 'json', 'Content-type: application/json' );
        $data[ ] = array( 'application/json', 'Content-type: application/json' );

        $exceptionName = '\Miao\Office\Header\Exception';
        //$data[] = array( 'unknownType', '', $exceptionName );
        return $data;
    }

    /**
     * @dataProvider providerTestEncoding
     */
    public function testEncoding( $contentType, $encoding, $expected, $exceptionName = '' )
    {
        if ( !empty( $exceptionName ) )
        {
            $this->setExpectedException( $exceptionName );
        }

        $headerObj = $this->_headerObj;
        $headerObj->setContentType( $contentType );
        $headerObj->setEncoding( $encoding );

        $actual = $headerObj->getEncoding();
        $this->assertEquals( strtoupper( $encoding ), $actual );

        $actual = $headerObj->getContentType();
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestEncoding()
    {
        $data = array();

// 		$data[] = array( 'html', 'utf-8', 'text/html; charset=UTF-8' );
// 		$data[] = array(
// 			'html',
// 			'windows-1251',
// 			'text/html; charset=WINDOWS-1251' );
// 		$data[] = array( 'json', 'utf-8', 'application/json; charset=UTF-8' );

        $exceptionName = '\Miao\Office\Header\Exception';
        $data[ ] = array( '', 'utf-8', '', $exceptionName );

        return $data;
    }
}