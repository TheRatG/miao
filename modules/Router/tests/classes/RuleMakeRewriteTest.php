<?php
/**
 * @author vpak
 * @date 2013-09-17 16:25:49
 */

namespace Miao\Router;

class RuleMakeRewriteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProviderTestMakeRewrite
     */
    public function testMakeRewrite( array $config, $expected )
    {
        $route = \Miao\Router\Rule::factory( $config );
        $actual = $route->makeRewrite();

        $this->assertEquals( $expected, $actual );
    }

    public function dataProviderTestMakeRewrite()
    {
        $data = array();

        $factory = new \Miao\Office\Factory();
        $viewRequestName = $factory->getViewRequestName();
        $actionRequestName = $factory->getActionRequestName();

        $config = array(
            'prefix' => 'Miao\\TestOffice2',
            'type' => \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
            'name' => 'Article\\Item',
            'desc' => 'Article item page. Some notes.',
            'rule' => '/article/:id',
            'validators' => array( array( 'id' => 'id', 'type' => 'notEmpty' ) )
        );
        $data[ ] = array(
            $config,
            '# View:Article\\Item Article item page. Some notes.' . "\n" . 'RewriteRule ^article/([^/]+)$ index.php?id=$1&' . $viewRequestName . '=Article\\Item&_prefix=Miao\\TestOffice2 [L,QSA]'
        );

        $config = array(
            'prefix' => 'Miao\\TestOffice',
            'type' => \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
            'name' => 'Article\\Item',
            'rule' => '/article/:id',
            'validators' => array( array( 'id' => 'id', 'type' => 'Numeric' ) )
        );
        $data[ ] = array(
            $config,
            '# View:Article\\Item' . "\n" . 'RewriteRule ^article/([0-9]+)$ index.php?id=$1&' . $viewRequestName . '=Article\\Item&_prefix=Miao\\TestOffice [L,QSA]'
        );

        $config = array(
            'prefix' => 'Miao\\TestOffice',
            'type' => \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_ACTION,
            'name' => 'Article\\Item',
            'rule' => '/article/:section',
            'validators' => array(
                array(
                    'id' => 'section',
                    'type' => 'In',
                    'variants' => 'lifestyle,finance'
                )
            )
        );

        $data[ ] = array(
            $config,
            '# Action:Article\\Item' . "\n" . 'RewriteRule ^article/(lifestyle|finance)$ index.php?section=$1&' . $actionRequestName . '=Article\\Item&_prefix=Miao\\TestOffice [L,QSA]'
        );

        $config = array(
            'prefix' => 'Miao\\TestOffice',
            'type' => \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
            'name' => 'Article\\Item',
            'rule' => '/:page/:id/:part/:user/:mode/:param',
            'validators' => array(
                array( 'id' => 'id', 'type' => 'Numeric' ),
                array( 'id' => 'part', 'type' => 'Numeric', 'max' => 5, 'min' => 0 ),
                array( 'id' => 'user', 'type' => 'Numeric', 'min' => 32, 'max' => 32 ),
                array( 'id' => 'page', 'type' => 'Numeric', 'min' => 1 ),
                array( 'id' => 'mode', 'type' => 'Numeric', 'min' => 2 ),
                array( 'id' => 'param', 'type' => 'Numeric', 'min' => 3, 'max' => 5 )

            )
        );
        $data[ ] = array(
            $config,
            '# View:Article\\Item' . "\n" . 'RewriteRule ^([0-9]+)/([0-9]+)/([0-9]+)/([0-9]{32})/([0-9]{2,})/([0-9]{3,5})$ index.php?page=$1&id=$2&part=$3&user=$4&mode=$5&param=$6&' . $viewRequestName . '=Article\\Item&_prefix=Miao\\TestOffice [L,QSA]'
        );

        return $data;
    }
}