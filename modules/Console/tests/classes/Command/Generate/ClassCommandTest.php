<?php
namespace Miao\Path\Command\Generate;

class ClassCommandTest extends \PHPUnit_Framework_TestCase
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
        $this->_app->add( new \Miao\Console\Command\Generate\ClassCommand );
    }

    public function testGenerateClass()
    {
        $className = 'Miao_TModule';

        $command = $this->_app->find( 'miao:generate-class' );
        $commandTester = new \Symfony\Component\Console\Tester\CommandTester( $command );
        $commandTester->execute(
            array( 'command' => $command->getName(), 'name' => $className )
        );

        $this->assertRegExp( '/Generate class/', $commandTester->getDisplay() );
        $moduleRoot = $this->_path->getModuleDir( $className );

        $filename = \Miao\Autoload::getInstance()
            ->getFilenameByClassName( $className );
        $this->assertTrue( file_exists( $filename ) );

        \Miao\Path\Helper::removeDir( $moduleRoot );
    }
}