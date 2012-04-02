<?php 
abstract class Miao_Office_ViewHelper_JsCssList
{
	protected $_dataHelperJsCssList;	
	
	public function __construct( $dataHelperJsCssList )
	{
		$this->_dataHelperJsCssList = $dataHelperJsCssList;
	}
	
	public function getdataHelperJsCssList()
	{
		return $this->_dataHelperJsCssList;
	}
		
	static protected function _getInstance( $className, Miao_Office_DataHelper_JsCssList $dataHelperJsCssList )
	{
		$index = 'dh:' . $className;
		$result = null;
		if ( !Miao_Registry::isRegistered( $index ) )
		{
			$result = new $className( $dataHelperJsCssList );			
			Miao_Registry::set( $index, $result );
		}
		else
		{
			$result = Miao_Registry::get( $index );
		}
		return $result;
	}
	
	public function js()
	{
		$result = '';
		
		$list = $this->getDataHelperJsCssList()->getJsSrcList();
		foreach ( $list as $value )
		{
			$result .= sprintf( "\n\t<script src=\"%s\"></script>", $value );
		}
		
		return $result;
	}

	public function css()
	{ 
		$result = '';
		
		$list = $this->getDataHelperJsCssList()->getCssSrcList();
		foreach ( $list as $value )
		{
			$result .= sprintf( "\n\t<link href=\"%s\" rel=\"stylesheet\">", $value );
		}
		
		return $result;
	}
}