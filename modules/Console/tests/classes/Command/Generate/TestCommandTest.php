<?php
/**
 * @author vpak
 * @date 2013-08-12 17:27:12
 */

namespace Miao\Console\Command\Generate;

class TestCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Console\Application
     */
    private $_app;

    /**
     * @var \Miao\Path
     */
    private $_path;

    private $_module = 'Miao\\TestOffice';

    public function setUp()
    {
        $this->_path = \Miao\App::getInstance()
            ->getPath();

        $this->_app = new \Symfony\Component\Console\Application();
        $this->_app->add( new \Miao\Console\Command\Generate\TestCommand );

        $moduleRoot = $this->_path->getModuleDir( $this->_module );
        \Miao\Path\Helper::removeDir( $moduleRoot );
    }

    public function tearDown()
    {
        $moduleRoot = $this->_path->getModuleDir( $this->_module );
        \Miao\Path\Helper::removeDir( $moduleRoot );
    }

    public function testCommand()
    {
        $className = $this->_module . '\\MetaTest';

        $command = $this->_app->find( 'miao:generate-test' );
        $commandTester = new \Symfony\Component\Console\Tester\CommandTester( $command );
        $commandTester->execute(
            array( 'command' => $command->getName(), 'name' => $className )
        );

        $filename = \Miao\Autoload::getInstance()
            ->getFilenameByClassName( $className );
        $this->assertFileExists( $filename );
    }

    public function testCommand2()
    {
        $className = 'Miao\\TestOfficeTest';

        $command = $this->_app->find( 'miao:generate-test' );
        $commandTester = new \Symfony\Component\Console\Tester\CommandTester( $command );
        $commandTester->execute(
            array( 'command' => $command->getName(), 'name' => $className )
        );

        $filename = \Miao\Autoload::getInstance()
            ->getFilenameByClassName( $className );
        $this->assertFileExists( $filename );
    }
}