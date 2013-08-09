<?php
namespace Miao\Path\Command\Generate;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ModuleTest extends \PHPUnit_Framework_TestCase
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
        $this->_app->add( new \Miao\Console\Command\Generate\Module );
    }

    public function testGenerateModule()
    {
        $command = $this->_app->find( 'miao:generate-module' );
        $commandTester = new CommandTester( $command );
        $commandTester->execute(
            array( 'command' => $command->getName(), 'name' => '\\Miao\\TModule' )
        );

        $moduleRoot = $this->_path->getModuleDir( '\\Miao\\TModule' );

        // --- dump ---
        echo __FILE__ . __METHOD__ . chr( 10 );
        var_dump( $commandTester->getDisplay() ) . chr( 10 );
        // --- // ---

        //$this->assertRegExp( 'Created', $commandTester->getDisplay() );

        \Miao\Path\Helper::removeDir( $moduleRoot );
    }
}