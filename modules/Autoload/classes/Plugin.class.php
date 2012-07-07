<?php
/**
 * Miao
 * Copyright (c) 2012, <OWNER>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * 	* Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 *  * Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in
 *	  the documentation and/or other materials provided with the
 *    distribution.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 * http://opensource.org/licenses/bsd-license
 */
/**
 *
 * Resolve class name to filename or something other
 * @author Vladimir
 *
 */

require_once 'Plugin/Standart.class.php';
require_once 'Plugin/IncludePath.class.php';
require_once 'Plugin/Zend.class.php';
require_once 'Plugin/PHPExcel.class.php';
require_once 'Plugin/PHPUnit.class.php';
require_once 'Plugin/Pheanstalk.class.php';
require_once 'Plugin/Pear.class.php';

abstract class Miao_Autoload_Plugin
{
	protected $_name;
	protected $_libPath;

	public function __construct( $name, $libPath )
	{
		if ( !file_exists( $libPath ) || !is_readable( $libPath ) )
		{
			$message = sprintf( 'Invalid param $libPath (%s): file doesn\'t exists or not readable', $libPath );
			throw new Miao_Autoload_Exception($message);
		}
		$this->_name = $name;
		$this->_libPath = $libPath;
	}

	/**
	 * @return the $_libPath
	 */
	public function getLibPath()
	{
		return $this->_libPath;
	}

	/**
	 * @return string the $_name
	 */
	public function getName()
	{
		return $this->_name;
	}

	abstract public function getFilenameByClassName( $className );

	/**
	 *
	 * Add new include path in position. Clear dublicate
	 *
	 * @param string $includePath new include path
	 * @param int $pos
	 * @param string $currentIncludePath (default = get_include_path())
	 */
	static public function addIncludePath( $includePath, $pos = false, $currentIncludePath = '' )
	{
		if ( empty( $currentIncludePath ) )
		{
			$currentIncludePath = get_include_path();
		}

		$curAr = explode( PATH_SEPARATOR, $currentIncludePath );
		$newAr = explode( PATH_SEPARATOR, $includePath );

		$curAr = array_unique( $curAr );
		$newAr = array_unique( $newAr );

		$curAr = array_diff( $curAr, $newAr );

		$pieces = array();
		if ( false === $pos )
		{
			$pieces = array_merge( $curAr, $newAr );
		}
		else
		{
			$firstPart = array_slice( $curAr, 0, $pos );
			$secondPart = array_slice( $curAr, $pos );

			$pieces = array_merge( $firstPart, $newAr );
			$pieces = array_merge( $pieces, $secondPart );
		}

		$result = implode( PATH_SEPARATOR, $pieces );
		set_include_path( $result );
		return $result;
	}
}