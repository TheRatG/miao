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
        $this->_miaoApp = \Miao\App::getInstance();
        $this->_path = $this->_miaoApp->getPath();
    }

    public function _makeFile( \Miao\Autoload\ClassInfo $classInfo, $template, $search, $replace )
    {
        $classTemplateFolder = $this->_path->getTemplateDir( 'Miao\\Console\\Command\\Generate\\ClassCommand' );
        $classTemplateFilename = $classTemplateFolder . DIRECTORY_SEPARATOR . $template;

        $libName = $classInfo->getLib();
        $className = $classInfo->getClass();
        $ar = explode( $classInfo->getDelimiter(), $className );
        $name = array_pop( $ar );

        $plugin = new \Miao\Autoload\Plugin\Standart( $libName, $this->_path->getRootDir( $libName ) );
        $classFilename = $plugin->getFilenameByClassName( $className );

        $string = file_get_contents( $classTemplateFilename );

        $search = array_merge(
            array(
                 '%namespace%',
                 '%date%',
                 '%class%'
            ), $search
        );
        $replace = array_merge(
            array(
                 $classInfo->getNamespace(),
                 date( 'Y-m-d H:i:s' ),
                 $name
            ), $replace
        );

        $string = str_replace(
            $search, $replace, $string
        );

        if ( file_exists( $classFilename ) )
        {
            $msg = sprintf( 'Class "%s" exists by file (%s)', $className, $classFilename );
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