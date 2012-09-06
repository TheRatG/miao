<?php
class Miao_Session_Namespace_Reference
{
	private $reference;

	public function __construct( &$reference )
	{
		$this->reference = &$reference;
	}

	public function &getReference()
	{
		$reference = &$this->reference;
		return $reference;
	}
}