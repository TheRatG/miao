<?php
class Miao_Office_TemplatesEngine_PhpNative extends Miao_TemplatesEngine_PhpNative
{
	protected $_listBlock = array();
	protected $_viewTemplate;

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

	protected function _includeBlock( $name, $before = '', $after = '' )
	{
		$result = '';
		if ( array_key_exists( $name, $this->_listBlock ) )
		{
			$block_obj = $this->_listBlock[ $name ];

			try
			{
				call_user_func_array( array(
					$block_obj,
					'process' ), $block_obj->getProcessParams() );
				$result .= $block_obj->fetch();
			}
			catch ( Miao_TemplatesEngine_Exception_Critical $e )
			{
				throw $e; // re-throw exception to the outer catch block
			}
			catch ( Exception $e )
			{
				$message = $this->_exceptionToString( $e );
				$this->getLogObj()->log( $this->_exceptionToString( $e ), Miao_Log::ERR );
				if ( $this->getDebugMode() )
				{
					$result .= $message;
				}
			}
		}

		$trmRes = trim( $result );
		if ( !empty( $trmRes ) )
		{
			$result = $before . $result . $after;
		}

		return $result;
	}
}