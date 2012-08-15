<?php
class Miao_Office_Resource_Get extends Miao_Office_Resource
{
	public function _initialize()
	{

	}

	/**
	 * (non-PHPdoc)
	 * @see Miao_Office_Resource::sendResponse()
	 */
	public function sendResponse( $sendHeader = true, $sendContent = true )
	{
		if ( !$this->_checkExistsOperationObject() )
		{
			throw new Miao_Office_Resource_Exception_OperaionNotFound( $this->_frontOffice );
		}

		$content = '';
		$view = $this->getOffice()->getView();
		$viewBlock = $this->getOffice()->getViewBlock();
		$action = $this->getOffice()->getAction();

		if ( !empty( $view ) )
		{
			$content = $view->fetch();
		}
		else if ( !empty( $viewBlock ) )
		{
			$viewBlock->process();
			$content = $viewBlock->fetch();
		}
		else if ( !empty( $action ) )
		{
			$content = $action->execute();
		}

		if ( !empty( $content ) && !$this->getContent() )
		{
			$this->setContent( $content );
		}

		if ( $sendHeader )
		{
			$this->sendHeader();
		}
		$content = $this->getContent();
		if ( $sendContent )
		{
			echo $content;
		}
		return $content;
	}
}