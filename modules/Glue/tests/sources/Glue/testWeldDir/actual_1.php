<?php


function testA()
{
	$obj = new A();
	return $obj;
}
function testB( $b )
{
	return $b * 2;
}
class A
{
	public function habr()
	{
		$a = 8 * 9;
		return $a;
	}
}
