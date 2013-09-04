<?php
/**
 * @author vpak
 * @date 2013-09-02 17:45:48
 */

namespace Miao\Office;

class FactoryTest extends \PHPUnit_Framework_TestCase
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
     * @dataProvider providerGetControllerClassName
     * @param array $config
     * @param array $params
     * @param array $defaultParams
     * @param $expected
     * @param string $exceptionName
     */
    public function testGetControllerClassName( array $config, array $params, $expected, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }

        $defaultPrefix = $config[ 'defaultPrefix' ];
        $defaultParams = isset( $config[ 'defaultParams' ] ) ? $config[ 'defaultParams' ] : array();
        $factory = new \Miao\Office\Factory( $defaultPrefix );
        $actual = $factory->getControllerClassName( $params, $defaultParams );

        $this->assertEquals( $expected, $actual );
    }

    public function providerGetControllerClassName()
    {
        $factory = new \Miao\Office\Factory();

        $data = array();

        $config = array(
            'defaultPrefix' => '\\Project\\Office',
            'defaultParams' => array()
        );
        $data[ ] = array(
            $config,
            array( $factory->getViewRequestName() => 'Main' ),
            '\\Project\\Office\\View\\Main',
            ''
        );

        $config = array(
            'defaultPrefix' => '\\Miao\\BackOffice',
            'defaultParams' => array()
        );
        $data[ ] = array(
            $config,
            array( $factory->getViewRequestName() => 'Main' ),
            array(),
            '\\Miao\\BackOffice\\View\\Main',
            ''
        );

        $config = array(
            'defaultPrefix' => 'Miao\\BackOffice',
            'defaultParams' => array()
        );
        $data[ ] = array(
            $config,
            array( $factory->getActionRequestName() => 'Article\\Add' ),
            array(),
            'action' => 'Miao\\BackOffice\\Action\\Article\\Add'
        );

        $config = array(
            'defaultPrefix' => '\\Miao\\BackOffice',
            'defaultParams' => array( $factory->getViewRequestName() => 'Main' )
        );
        $data[ ] = array(
            $config,
            array( $factory->getViewRequestName() => 'Main2' ),
            '\\Miao\\BackOffice\\View\\Main2'
        );

        $config = array(
            'defaultPrefix' => '\\Miao\\BackOffice',
            'defaultParams' => array( $factory->getViewRequestName() => 'Main' )
        );
        $data[ ] = array(
            $config,
            array( $factory->getViewRequestName() => 'Main2' ),
            '\\Miao\\BackOffice\\View\\Main2'
        );

        // test default
        $config = array(
            'defaultPrefix' => '\\Miao\\BackOffice',
            'defaultParams' => array( $factory->getViewRequestName() => 'Main' )
        );
        $data[ ] = array(
            $config,
            array(),
            '\\Miao\\BackOffice\\View\\Main'
        );

        $config = array(
            'defaultPrefix' => '\\Miao\\BackOffice',
            'defaultParams' => array( $factory->getActionRequestName() => 'Main' )
        );
        $data[ ] = array(
            $config,
            array( $factory->getViewRequestName() => '' ),
            '\\Miao\\BackOffice\\Action\\Main'
        );

        $config = array(
            'defaultPrefix' => '\\Miao\\BackOffice',
            'defaultParams' => array( $factory->getViewBlockRequestName() => 'Main' ),
        );
        $data[ ] = array(
            $config,
            array(),
            '\\Miao\\BackOffice\\ViewBlock\\Main'
        );

        $config = array(
            'defaultPrefix' => '\\Miao\\BackOffice',
            'defaultParams' => array(),
        );
        $data[ ] = array(
            $config,
            array(),
            array(),
            ''
        );

        $config = array(
            'defaultPrefix' => '\\Miao\\BackOffice',
        );
        $data[ ] = array(
            $config,
            array( $factory->getViewBlockRequestName() => 'Main', $factory->getActionRequestName() => 'Main' ),
            '',
            '\\Miao\\Office\\Exception'
        );
        return $data;
    }
}