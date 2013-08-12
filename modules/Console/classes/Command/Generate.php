<?php
/**
 * @author vpak
 * @date 2013-08-12 17:10:37
 */

namespace Miao\Console\Command;

use Miao\Console\Exception;

class Generate extends \Symfony\Component\Console\Command\Command
{
    private $_miaoApp;

    private $_path;

    public function __construct()
    {
        parent::__construct();
        $this->_miaoApp = \Miao\Application::getInstance();
        $this->_path = $this->_miaoApp->getPath();
    }

    public function _makeFile( \Miao\Autoload\ClassInfo $classInfo, $template, $author )
    {
        $classTemplateFolder = $this->_path->getTemplateDir( '\\Miao\\Console\\Generate\\ClassCommand' );
        $classTemplateFilename = $classTemplateFolder . DIRECTORY_SEPARATOR . $template;

        $libName = $classInfo->getLib();
        $name = $classInfo->getClass();

        $plugin = new \Miao\Autoload\Plugin\Standart( $libName, $this->_path->getRootDir( $libName ) );
        $classFilename = $plugin->getFilenameByClassName( $name );

        $string = file_get_contents( $classTemplateFilename );
        $string = str_replace(
            array(
                 '%author%',
                 '%namespace%',
                 '%date%',
                 '%class%'
            ), array(
                    $author,
                    $classInfo->getNamespace(),
                    date( 'Y-m-d H:i:s' ),
                    $name
               ), $string
        );

        if ( file_exists( $classFilename ) )
        {
            $msg = sprintf( 'Class "%s" exists by file (%s)', $name, $classFilename );
            throw new Exception( $msg );
        }

        $dir = dirname( $classFilename );
        if ( !file_exists( $dir ) )
        {
            mkdir( $dir, 0775, true );
        }
        file_put_contents( $classFilename, $string );

        return $classFilename;
    }
}