<?php
class Miao_TestOffice_View_Article_List extends Miao_Office_View
{
	protected function _initializeBlock()
	{
		$this->setTmplVars( 'title', 'Title1' );

		$this->_addBlock( 'Article_List', 'Miao_TestOffice_ViewBlock_Article_List' );
	}
}