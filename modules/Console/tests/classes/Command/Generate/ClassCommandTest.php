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

    private $_module = 'Miao\\TestOffice';

    public function setUp()
    {
        $this->_path = \Miao\App::getInstance()
            ->getPath();
        require_once $this->_path->getRootDir() . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

        $this->_app = new \Symfony\Component\Console\Application();
        $this->_app->add( new \Miao\Console\Command\Generate\ModuleCommand );
        $this->_app->add( new \Miao\Console\Command\Generate\ClassCommand );

        $moduleRoot = $this->_path->getModuleDir( $this->_module );
        \Miao\Path\Helper::removeDir( $moduleRoot );
    }

    public function tearDown()
    {
        $moduleRoot = $this->_path->getModuleDir( $this->_module );
        \Miao\Path\Helper::removeDir( $moduleRoot );
    }

    public function testGenerateClass()
    {
        $className = $this->_module;

        $command = $this->_app->find( 'miao:generate-class' );
        $commandTester = new \Symfony\Component\Console\Tester\CommandTester( $command );
        $commandTester->execute(
            array( 'command' => $command->getName(), 'name' => $className )
        );

        $this->assertRegExp( '/Generated file/', $commandTester->getDisplay() );
        $moduleRoot = $this->_path->getModuleDir( $className );

        $filename = \Miao\Autoload::getInstance()
            ->getFilenameByClassName( $className );
        $this->assertTrue( file_exists( $filename ) );

        try
        {
            $commandTester->execute(
                array( 'command' => $command->getName(), 'name' => $className )
            );
        }
        catch ( \Miao\Console\Exception $e )
        {
        }
    }

    public function testView()
    {
        $className = $this->_module . '\\View\\Main';

        $command = $this->_app->find( 'miao:generate-class' );
        $commandTester = new \Symfony\Component\Console\Tester\CommandTester( $command );
        $commandTester->execute(
            array( 'command' => $command->getName(), 'name' => $className )
        );

        $filename = \Miao\Autoload::getInstance()
            ->getFilenameByClassName( $className );
        $this->assertFileExists( $filename );

        $content = file_get_contents( $filename );
        $this->assertRegExp( '/class Main extends \\\\Miao\\\\TestOffice\\\\View/', $content );

        $filename = \Miao\Autoload::getInstance()
            ->getPlugin( 'Miao' )
            ->getFilenameByClassName( $this->_module . '\\View' );

        $this->assertFileExists( $filename );

        $content = file_get_contents( $filename );
        $this->assertRegExp( '/class View extends \\\\Miao\\\\Office\\\\View/', $content );
    }
}