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
 * * Redistributions of source code must retain the above copyright
 * notice, this list of conditions and the following disclaimer.
 * * Redistributions in binary form must reproduce the above copyright
 * notice, this list of conditions and the following disclaimer in
 * the documentation and/or other materials provided with the
 * distribution.
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

require_once 'Exception.class.php';
require_once 'Plugin.class.php';

class Miao_Autoload
{
	static private $_instance;

	private $_registerList = array();
	private $_history = array();

	private function __construct()
	{

	}

	private function __clone()
	{

	}

	static public function getInstance()
	{
		if ( is_null( self::$_instance ) )
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	static public function register( array $autoloadConfig )
	{
		$auto = self::getInstance();
		foreach ( $autoloadConfig as $configItem )
		{
			$auto->checkConfigItem( $configItem );
			if ( 0 !== strcasecmp( $configItem[ 'plugin' ], 'None' ) )
			{
				$auto->registerItem( $configItem[ 'name' ], $configItem[ 'plugin' ], $configItem[ 'path' ] );
			}
		}
		$res = spl_autoload_register( array( $auto, 'autoload' ), true );
		if ( !$res )
		{
			throw new Exception( 'Maio autoload didn\'t register' );
		}
	}

	static public function getFilenameByClassName( $className )
	{
		$auto = self::getInstance();
		$result = '';
		$auto->_history = array();

		foreach ( $auto->getRegisterList() as $item )
		{
			$filename = $item->getFilenameByClassName( $className );
			$auto->_history[] = $filename;
			if ( file_exists( $filename ) )
			{
				$result = $filename;
				break;
			}
		}
		return $result;
	}

	public function registerItem( $name, $plugin, $libPath )
	{
		$index = $this->_getIndex( $name );
		$className = 'Miao_Autoload_Plugin_' . $plugin;
		$plugin = new $className( $libPath );

		$this->registerPlugin( $index, $plugin );
	}

	public function registerPlugin( $name, Miao_Autoload_Plugin $plugin )
	{
		$this->_registerList[ $name ] = $plugin;
	}

	public function autoload( $className )
	{
		$filename = $this->getFilenameByClassName( $className );
		if ( !empty( $filename ) && file_exists( $filename ) )
		{
			require_once $filename;
			if ( !class_exists( $className, false ) && !interface_exists( $className, false ) )
			{
				$message = sprintf( 'Class (%s) not found (%s)', $className, $filename );
				$this->_throwException( $className, 'Miao_Autoload_Exception_ClassNotFound', $message );
			}
			else
			{
				return true;
			}
		}
		else
		{
			$message = sprintf( 'File not found for class "%s": %s', $className, print_r( $this->_history, true ) );
			$this->_throwException( $className, 'Miao_Autoload_Exception_FileNotFound', $message );
		}
	}

	public function getRegisterList()
	{
		return $this->_registerList;
	}

	public function getPlugin( $name )
	{
		$index = $this->_getIndex( $name );
		$result = null;
		if ( isset( $this->_registerList[ $index ] ) )
		{
			$result = $this->_registerList[ $index ];
		}
		return $result;
	}

	public function checkConfigItem( array $configItem )
	{
		$requireAttr = array( 'name', 'path', 'plugin' );
		foreach ( $requireAttr as $paramName )
		{
			if ( !isset( $configItem[ $paramName ] ) )
			{
				$message = sprintf( 'Invalid config item (%s), does not exists param (%s)', print_r( $configItem, true ), $paramName );
				throw new Miao_Autoload_Exception_InvalidConfig( $message );
			}
		}

		if ( !file_exists( $configItem[ 'path' ] ) )
		{
			$message = sprintf( 'Invalid config item (path): file (%s) doesn\'t exists', $configItem[ 'path' ] );

			throw new Miao_Autoload_Exception_InvalidConfig( $message );
		}

		return true;
	}

	protected function _throwException( $className, $exceptionClassName, $exceptionMessage = '' )
	{
		$evalString = sprintf( 'class %s
			{
				public function __construct()
				{
					$message = "%s";
					throw new %s( $message );
				}

				static function __callstatic( $m, $args )
				{
					$message = "%s";
					throw new %s( $message );
				}
			}
				', $className, addslashes( $exceptionMessage ), $exceptionClassName, addslashes( $exceptionMessage ), $exceptionClassName );
		return eval( $evalString );
	}

	private function _getIndex( $name )
	{
		$result = strtolower( trim( $name ) );
		return $result;
	}
}