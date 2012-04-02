<?php
class Miao_TestOffice_ViewBlock_TmplArray extends Miao_Office_ViewBlock
{
	protected function _processData()
	{

	}

	protected function _setTemplateVariables()
	{
		return array( 'title' => 'title', 'body' => 'TmplArray body' );
	}
}