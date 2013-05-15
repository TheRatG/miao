<?php
/**
 * @author vpak
 * @date 2013-05-14 09:30:30
 */
class Miao_Form_Control_Group_Item
{
	/**
	 *
	 * @var Miao_Form_Control_Group
	 */
	protected $_group;

	/**
	 *
	 * @var array Miao_Form_Control
	 */
	protected $_controlList = array();

	public function __construct( Miao_Form_Control_Group $group )
	{
		$this->_group = $group;
	}

	public function __get( $name )
	{
		$result = null;
		if ( array_key_exists( $name, $this->_controlList ) )
		{
			$result = $this->_controlList[ $name ];
		}
		else
		{
			$message = sprintf(
				'Attribute "%s" not found. Support controls name - %s', $name,
				print_r( array_keys( $this->_controlList ), true ) );
			throw new Miao_Form_Control_Group_Item_Exception( $message );
		}
		return $result;
	}

	public function validate()
	{
		$result = true;
		foreach ( $this->_controlList as $control )
		{
			$result = $control->validate() && $result;
		}
		return $result;
	}

	public function render()
	{
		$result = array();
		foreach ( $this->_controlList as $control )
		{
			$result[] = $control->render();
		}
		$result = implode( "\n", $result );
		return $result;
	}

	public function addControl( $key, Miao_Form_Control $control )
	{
		$newControl = clone $control;
		$newControl->setValue( null );

		$controlName = $control->getName();
		$name = $this->_makeName( $key, $controlName );
		$newControl->setName( $name );

		$this->_controlList[ $controlName ] = $newControl;
		return $this;
	}

	protected function _makeName( $key, $controlName )
	{
		$groupName = $this->_group->getName();
		if ( $groupName )
		{
			$name = sprintf( '%s[%s][%s]', $groupName, $controlName, $key );
		}
		else
		{
			$name = sprintf( '%s[%s]', $controlName, $key );
		}
		return $name;
	}

	public function __destruct()
	{
		$this->_group = null;
		unset( $this->_controlList );
	}
}