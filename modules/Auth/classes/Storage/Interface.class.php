<?php
interface Miao_Auth_Storage_Interface
{

	public function isEmpty();

	public function read();

	public function write( $contents );

	public function clear();
}