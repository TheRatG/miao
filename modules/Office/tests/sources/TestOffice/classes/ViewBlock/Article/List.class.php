<?php
class Miao_TestOffice_ViewBlock_Article_List extends Miao_Office_ViewBlock
{
	protected $_list;

	protected function _processData()
	{
		$this->_list = array( 1, 2, 3 );
	}

	protected function _setTemplateVariables()
	{
		$this->_setTmplVar( 'list', $this->_list );
	}
}