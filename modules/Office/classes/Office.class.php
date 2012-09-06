<?php
/**
 *
 * Facade
 *
 * @author vpak
 */
class Miao_Office
{
	const TYPE_FACTORY = 'Factory';
	const TYPE_HEADER = 'Header';
	const TYPE_PATH = 'Path';

	const TYPE_RESOURCE = 'Resource';

	const TYPE_VIEW = 'View';
	const TYPE_VIEWBLOCK = 'ViewBlock';
	const TYPE_ACTION = 'Action';

	private $_factory;
	private $_header;
	private $_path;

	/**
	 *
	 * Enter description here ...
	 * @var Miao_Office_Resource
	 */
	private $_resource;

	private $_action;
	private $_view;
	private $_viewBlock;

	/**
	 * @return the $_factory
	 */
	public function getFactory()
	{
		return $this->_factory;
	}

	/**
	 * @param field_type $_factory
	 */
	public function setFactory( $factory )
	{
		$this->_factory = $factory;
	}

	/**
	 * @return the $_resource
	 */
	public function getResource()
	{
		return $this->_resource;
	}

	/**
	 * @param field_type $_resource
	 */
	public function setResource( $resource )
	{
		$this->_resource = $resource;
		$this->_resource->setOffice( $this );
	}

	/**
	 * @return Miao_Office_Header $_header
	 */
	public function getHeader()
	{
		return $this->_header;
	}

	/**
	 * @param field_type $_header
	 */
	public function setHeader( $header )
	{
		$this->_header = $header;
	}

	/**
	 * @return the $_action
	 */
	public function getAction()
	{
		return $this->_action;
	}

	/**
	 * @param field_type $_action
	 */
	public function setAction( $action )
	{
		$this->_action = $action;
		$this->_action->setOffice( $this );
	}

	/**
	 * @return the $_view
	 */
	public function getView()
	{
		return $this->_view;
	}

	/**
	 * @param field_type $_view
	 */
	public function setView( $view )
	{
		$this->_view = $view;
		$this->_view->setOffice( $this );
	}

	/**
	 * @return the $_viewBlock
	 */
	public function getViewBlock()
	{
		return $this->_viewBlock;
	}

	/**
	 * @param field_type $_viewBlock
	 */
	public function setViewBlock( $viewBlock )
	{
		$this->_viewBlock = $viewBlock;
	}

	public function sendResponse( $sendHeader = true, $sendContent = true )
	{
		$resource = $this->getResource();
		$content = $resource->sendResponse( $sendHeader, $sendContent );
		return $content;
	}

	static public function getTypesObject()
	{
		$result = array(
			self::TYPE_FACTORY,
			self::TYPE_HEADER,
			self::TYPE_PATH,

			self::TYPE_RESOURCE,

			self::TYPE_VIEW,
			self::TYPE_VIEWBLOCK,
			self::TYPE_ACTION );
		return $result;
	}

	static public function getTypesObjectRequest()
	{
		$result = array( self::TYPE_RESOURCE,

		self::TYPE_VIEW, self::TYPE_VIEWBLOCK, self::TYPE_ACTION );
		return $result;
	}
}