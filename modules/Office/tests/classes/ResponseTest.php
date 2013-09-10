<?php
/**
 * @author vpak
 * @date 2013-09-06 10:43:53
 */

namespace Miao\Office;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerTestSetContent
     * @param $content
     * @param string $exceptionName
     */
    public function testSetContent( $content, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }
        $response = new \Miao\Office\Response();
        $response->setContent( $content );

        $this->assertEquals( $content, $response->getContent() );
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestSetContent()
    {
        $data = array();

        $data[ ] = array( 'text' );
        $data[ ] = array( 12345 );
        $data[ ] = array( new \stdClass(), '\\Miao\\Office\\Response\\Exception\\UnexpectedValue' );
        $data[ ] = array( array( '1', '2', '3' ), '\\Miao\\Office\\Response\\Exception\\UnexpectedValue' );

        return $data;
    }

    /**
     * @dataProvider providerTestSetStatusCode
     * @param $code
     * @param string $text
     * @param string $exceptionName
     */
    public function testSetStatusCode( $code, $text = '', $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }
        $response = new \Miao\Office\Response();
        $response->setStatusCode( $code, $text );

        $this->assertEquals( $code, $response->getStatusCode() );
        if ( $text )
        {
            $this->assertEquals( $text, $response->getStatusText() );
        }
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestSetStatusCode()
    {
        $data = array();

        $data[ ] = array( 1, null, '\\Miao\\Office\\Response\\Exception\\InvalidArgument' );
        $data[ ] = array( 404 );
        $data[ ] = array( 404, 'My not found' );

        return $data;
    }

    public function testSetHeader()
    {
    }
}