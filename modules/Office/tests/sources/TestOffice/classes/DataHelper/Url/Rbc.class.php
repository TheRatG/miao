<?php
class Miao_TestOffice_DataHelper_Url_Rbc
	extends Miao_Office_DataHelper_Url implements Miao_Office_DataHelper_Url_Interface
{
	/**
	 *
	 * @return Miao_Office_DataHelper_Url
	 */
	static public function getInstance()
	{
		return parent::_getInstance( __CLASS__ );
	}

	protected function _init()
	{
		$this->setHost( 'http://rbc.ru' );
		$this->setPics( 'http://pics.rbc.ru/images' );
	}
}