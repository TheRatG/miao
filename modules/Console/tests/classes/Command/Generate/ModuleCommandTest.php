<?php
namespace Miao\Path\Command\Generate;

class ModuleCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Console\Application
     */
    private $_app;

    /**
     * @var \Miao\Path
     */
    private $_path;

    public function setUp()
    {
        $this->_path = \Miao\Application::getInstance()
            ->getPath();
        require_once $this->_path->getRootDir() . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

        $this->_app = new \Symfony\Component\Console\Application();
        $this->_app->add( new \Miao\Console\Command\Generate\ModuleCommand );
    }

    public function testGenerateModule()
    {
        $command = $this->_app->find( 'miao:generate-module' );
        $commandTester = new \Symfony\Component\Console\Tester\CommandTester( $command );
        $commandTester->execute(
            array( 'command' => $command->getName(), 'name' => '\\Miao\\TModule' )
        );

        $moduleRoot = $this->_path->getModuleDir( '\\Miao\\TModule' );

        $this->assertRegExp( '/Created/', $commandTester->getDisplay() );
        $this->assertTrue( file_exists( $moduleRoot ) );

        $commandTester->execute(
            array( 'command' => $command->getName(), 'name' => '\\Miao\\TModule' )
        );
        $this->assertRegExp( '/exists and not empty/', $commandTester->getDisplay() );

        \Miao\Path\Helper::removeDir( $moduleRoot );
    }

    public function testError()
    {
        $command = $this->_app->find( 'miao:generate-module' );
        $commandTester = new \Symfony\Component\Console\Tester\CommandTester( $command );
        $commandTester->execute(
            array( 'command' => $command->getName(), 'name' => '\\Miao' )
        );

        $this->assertRegExp( '/Invalid module name/', $commandTester->getDisplay() );
    }
}