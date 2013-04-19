<?php
class Miao_Office_Header_Test extends PHPUnit_Framework_TestCase
{
	private $_headerObj = null;

	public function setUp()
	{
		$this->_headerObj = new Miao_Office_Header();
		$this->_headerObj->reset();
	}

	/**
	 *
	 * @dataProvider providerTestSet
	 * @param unknown_type $name
	 * @param unknown_type $value
	 * @param unknown_type $exception
	 */
	public function testSet( $name, $value, $exception = '' )
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

	public function providerTestSet()
	{
		$data = array();

		$data[] = array( 'Content-Disposition', 'inline; filename=result.pdf' );
		$data[] = array( 'Content-type', 'application/x-pdf' );

		return $data;
	}

	/**
	 *
	 * @dataProvider providerTestContentType
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

	public function providerTestContentType()
	{
		$data = array();

		$data[] = array( 'text/html', 'Content-type: text/html' );
		$data[] = array( 'html', 'Content-type: text/html' );
		$data[] = array( 'xml', 'Content-type: application/xml' );
		$data[] = array( 'application/xml', 'Content-type: application/xml' );
		$data[] = array( 'json', 'Content-type: application/json' );
		$data[] = array( 'application/json', 'Content-type: application/json' );

		$exceptionName = 'Miao_Office_Header_Exception';
		//$data[] = array( 'unknownType', '', $exceptionName );


		return $data;
	}

	/**
	 *
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

	public function providerTestEncoding()
	{
		$data = array();

// 		$data[] = array( 'html', 'utf-8', 'text/html; charset=UTF-8' );
// 		$data[] = array(
// 			'html',
// 			'windows-1251',
// 			'text/html; charset=WINDOWS-1251' );
// 		$data[] = array( 'json', 'utf-8', 'application/json; charset=UTF-8' );

		$exceptionName = 'Miao_Office_Header_Exception';
		$data[] = array( '', 'utf-8', '', $exceptionName );

		return $data;
	}
}