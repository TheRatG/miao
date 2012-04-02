<?php
class Miao_TestOffice_ViewBlock_Article_Slave extends Miao_Office_ViewBlock
{
	protected $_list;
	protected $_section;

	public function process()
	{
		$argList = func_get_args();

		$section = $argList[0];
		$this->_section = $section;

		parent::process();
	}

	protected function _processData()
	{
		$this->_list = array( 1, 2, 3 );
	}

	protected function _setTemplateVariables()
	{
		$this->_setTmplVar( 'list', $this->_list );
		$this->_setTmplVar( 'section', $this->_section );
	}
}