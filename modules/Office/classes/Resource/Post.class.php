<?php
class Miao_Office_Resource_Post extends Miao_Office_Resource
{
	public function _initialize()
	{

	}

	public function sendResponse( $sendHeader = true, $sendContent = true )
	{
		if ( !$this->_checkExistsOperationObject() )
		{
			throw new Miao_Office_Resource_Exception_OperaionNotFound( $this->_frontOffice );
		}

		$content = '';

		$action = $this->getOffice()->getAction();
		$view = $this->getOffice()->getView();
		$viewBlock = $this->getOffice()->getViewBlock();

		if ( !empty( $action ) )
		{
			$content = $action->execute();
		}
		else if ( !empty( $view ) )
		{
			$content = $view->fetch();
		}
		else if ( !empty( $viewBlock ) )
		{
			$viewBlock->process();
			$content = $viewBlock->fetch();
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