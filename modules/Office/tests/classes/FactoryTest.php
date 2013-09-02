<?php
/**
 * @author vpak
 * @date 2013-09-02 17:45:48
 */

namespace Miao\Office;

class FactoryTest
{
    /**
     * @dataProvider providerTestGetClassName
     * @param $defaultPrefix
     * @param $type
     * @param $name
     * @param $prefix
     * @param $expected
     * @param string $exceptionName
     */
    public function testGetClassName( $defaultPrefix, $type, $name, $prefix, $expected, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }
        $obj = new \Miao\Office\Factory( $defaultPrefix );
        $actual = $obj->getClassName( $type, $name, $prefix );

        $this->assertEquals( $expected, $actual );
    }

    public function providerTestGetClassName()
    {
        $data = array();

        $this->setUp();

        $data[ ] = array(
            '\\Project\\Office',
            \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_ACTION,
            'Article\\Del',
            '',
            '\\Project\\Office\\Action\\Article\\Del'
        );
        $data[ ] = array(
            '\\Project\\Office',
            \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_ACTION,
            'Article\\Add',
            '',
            '\\Project\\Office\\Action\\Article\\Add'
        );
        $data[ ] = array(
            '\\Project\\Office',
            \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
            'Main',
            '',
            '\\Project\\Office\\View\\Main'
        );
        $data[ ] = array(
            '\\Project\\Office',
            \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
            'Article\\List',
            '',
            '\\Project\\Office\\View\\Article\\List'
        );

        return $data;
    }

    /**
     * @dataProvider providerTestGetClassList
     * @param array $config
     * @param array $requestParams
     * @param array $default
     * @param $expected
     * @param string $exceptionName
     */
    public function testGetClassList( array $config, array $requestParams, array $default, $expected,
                                      $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }

        $defaultPrefix = $config[ 'defaultPrefix' ];
        $requestMethod = isset( $config[ 'requestMethod' ] ) ? $config[ 'requestMethod' ] : 'get';

        $obj = new \Miao\Office\Factory( $defaultPrefix, $requestMethod );
        $actual = $obj->getClassList( $requestParams, $default );
        $this->assertEquals( $expected, $actual );
    }

    public function providerTestGetClassList()
    {
        $data = array();

        $config = array(
            'defaultPrefix' => '\\Project\\Office'
        );
        $data[ ] = array(
            $config,
            array( '_view' => 'Main' ),
            array(),
            array(
                'resource' => '\\Miao\\Office\\Resource\\Get',
                'view' => '\\Project\\Office\\View\\Main',
                'viewBlock' => null,
                'action' => null
            ),
            ''
        );

        $config = array( 'defaultPrefix' => '\\Miao\\BackOffice' );
        $data[ ] = array(
            $config,
            array( '_view' => 'Main' ),
            array(),
            array(
                'resource' => '\\Miao\\Office\\Resource\\Get',
                'view' => '\\Miao\\BackOffice\\View\\Main',
                'viewBlock' => null,
                'action' => null
            ),
            ''
        );

        $config = array(
            'defaultPrefix' => 'Miao\\BackOffice',
            'requestMethod' => 'post'
        );
        $data[ ] = array(
            $config,
            array( '_action' => 'Article\\Add' ),
            array(),
            array(
                'resource' => '\\Miao\\Office\\Resource\\Post',
                'view' => '',
                'viewBlock' => '',
                'action' => 'Miao\\BackOffice\\Action\\Article\\Add'
            )
        );

        $config = array(
            'defaultPrefix' => '\\Miao\\BackOffice',
            'requestMethod' => 'post'
        );
        $data[ ] = array(
            $config,
            array(),
            array(),
            array(
                'resource' => '\\Miao\\Office\\Resource\\Post',
                'view' => null,
                'viewBlock' => null,
                'action' => null
            )
        );

        $config = array(
            'defaultPrefix' => '\\Miao\\BackOffice',
            'requestMethod' => 'get'
        );
        $data[ ] = array(
            $config,
            array( '_view' => 'Main2' ),
            array( '_view' => 'Main' ),
            array(
                'resource' => '\\Miao\\Office\\Resource\\Get',
                'view' => '\\Miao\\BackOffice\\View\\Main2',
                'viewBlock' => null,
                'action' => null
            )
        );

        $config = array(
            'defaultPrefix' => '\\Miao\\BackOffice',
            'requestMethod' => 'get'
        );
        $data[ ] = array(
            $config,
            array( '_view' => 'Main2' ),
            array( '_action' => 'Main' ),
            array(
                'resource' => '\\Miao\\Office\\Resource\\Get',
                'view' => '\\Miao\\BackOffice\\View\\Main2',
                'viewBlock' => null,
                'action' => null
            )
        );

        // test default
        $config = array(
            'defaultPrefix' => '\\Miao\\BackOffice',
            'requestMethod' => 'get'
        );
        $data[ ] = array(
            $config,
            array(),
            array( '_view' => 'Main' ),
            array(
                'resource' => '\\Miao\\Office\\Resource\\Get',
                'view' => '\\Miao\\BackOffice\\View\\Main',
                'viewBlock' => null,
                'action' => null
            )
        );

        $config = array(
            'defaultPrefix' => '\\Miao\\BackOffice',
            'requestMethod' => 'post'
        );
        $data[ ] = array(
            $config,
            array( '_view' => '' ),
            array( '_action' => 'Main' ),
            array(
                'resource' => '\\Miao\\Office\\Resource\\Post',
                'view' => null,
                'viewBlock' => null,
                'action' => '\\Miao\\BackOffice\\Action\\Main'
            )
        );

        $config = array(
            'defaultPrefix' => '\\Miao\\BackOffice',
            'requestMethod' => 'get'
        );
        $data[ ] = array(
            $config,
            array(),
            array( '_viewBlock' => 'Main' ),
            array(
                'resource' => '\\Miao\\Office\\Resource\\Get',
                'view' => null,
                'viewBlock' => '\\Miao\\BackOffice\\ViewBlock\\Main',
                'action' => null
            )
        );

        return $data;
    }
}