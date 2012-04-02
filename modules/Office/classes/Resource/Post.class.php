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
		if ( !empty( $action ) )
		{
			$content = $action->execute();
		}

		if ( !empty( $content ) && !$this->getContent() )
		{
			$this->setContent( $content );
		}

		if ( $sendHeader )
		{
			$this->_sendHeader();
		}
		$content = $this->getContent();
		if ( $sendContent )
		{
			echo $content;
		}
		return $content;
	}
}