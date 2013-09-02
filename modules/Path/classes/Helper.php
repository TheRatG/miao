<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vpak
 * Date: 02.09.13
 * Time: 15:04
 * To change this template use File | Settings | File Templates.
 */

namespace Miao\Path;

class Helper
{
    static public function isDirEmpty( $dir, $throwException = true )
    {
        $result = true;
        if ( !is_readable( $dir ) )
        {
            if ( $throwException )
            {
                $msg = sprintf( 'Dir (%s) is not readable', $dir );
                throw new Exception( $msg );
            }
            $result = null;
        }
        else
        {
            $handle = opendir( $dir );
            while ( false !== ( $entry = readdir( $handle ) ) )
            {
                if ( $entry != "." && $entry != ".." )
                {
                    $result = false;
                    break;
                }
            }
        }
        return $result;
    }

    static public function removeDir( $dir )
    {
        if ( is_dir( $dir ) )
        {
            $objects = scandir( $dir );
            foreach ( $objects as $object )
            {
                if ( $object != "." && $object != ".." )
                {
                    if ( filetype( $dir . "/" . $object ) == "dir" )
                    {
                        self::removeDir( $dir . "/" . $object );
                    }
                    else
                    {
                        unlink( $dir . "/" . $object );
                    }
                }
            }
            reset( $objects );
            rmdir( $dir );
        }
    }
}