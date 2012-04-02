<?php
abstract class Miao_Office_TemplatesEngine
{
	protected $_listBlock = array();
	protected $_viewTemplate = '';

	public function addBlock( $name, Miao_Office_ViewBlock $viewBlock )
	{
		$this->_listBlock[ $name ] = $viewBlock;
	}

	public function getBlock( $name )
	{
		$result = null;
		if ( isset( $this->_listBlock[ $name ] ) )
		{
			$result = $this->_listBlock[ $name ];
		}
		return $result;
	}

	public function setViewTemplate( $viewTemplate )
	{
		$this->_viewTemplate = $viewTemplate;
	}

	protected function _getViewTemplate()
	{
		return $this->_viewTemplate;
	}

	abstract public function fetch( $templateName );

	abstract protected function _includeBlock( $name );
}