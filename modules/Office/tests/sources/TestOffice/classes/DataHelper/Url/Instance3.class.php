<?php
class Miao_TestOffice_DataHelper_Url_Instance3
	extends Miao_Office_DataHelper_Url implements Miao_Office_DataHelper_Url_Interface
{
	static public function getInstance()
	{
		return parent::_getInstance( __CLASS__ );
	}

	protected function _init()
	{
		//$this->setHost( 'http://ya.ru' );
		$this->setPics( 'http://pics.ya.ru' );
	}
}