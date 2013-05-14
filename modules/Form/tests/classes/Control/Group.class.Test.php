<?php
/**
 * create, render, validate, load, save keys, balance controls
 *
 * @author vpak
 *
 */
class Miao_Form_Control_Group_Test extends PHPUnit_Framework_TestCase
{

	public function testCreateSimple()
	{
		$attributes = array();
		$url = new Miao_Form_Control_Text( 'url', $attributes );
		$group = new Miao_Form_Control_Group( 'group' );
		$group->addControl( $url );

		$list = $group->getItemList();
		$this->assertTrue( !empty( $list ) );

		$actual = 'group[url][]';
		foreach ( $list as $item )
		{
			$expected = $item->url->getName();
			$this->assertEquals( $expected, $actual );
		}
	}

	public function testCreateEmptyGroup()
	{
		$attributes = array();
		$url = new Miao_Form_Control_Text( 'url', $attributes );
		$group = new Miao_Form_Control_Group( '' );
		$group->setForceKeyEnable( true );
		$group->addControl( $url );

		$list = $group->getItemList();
		$this->assertTrue( !empty( $list ) );

		$actual = 'url[0]';
		foreach ( $list as $item )
		{
			$expected = $item->url->getName();
			$this->assertEquals( $expected, $actual );
		}
	}

	/**
	 * @dataProvider providerTestRender
	 * @param string $actualFilename
	 */
	public function testRender( Miao_Form_Control_Group $group, $actualFilename )
	{
		$sourceFolder = Miao_PHPUnit::getSourceFolder( __METHOD__ );

		$actual = file_get_contents( $sourceFolder . '/' . $actualFilename );
		$expected = $group->render();

		$this->assertEquals( $this->_clearSpace( $expected ),
			$this->_clearSpace( $actual ) );
	}

	public function providerTestRender()
	{
		$data = array();
		$attributes = array();
		$title = new Miao_Form_Control_Text( 'title', $attributes );
		$url = new Miao_Form_Control_Text( 'url', $attributes );

		$group = new Miao_Form_Control_Group( 'group' );
		$group->addControl( $title );
		$group->addControl( $url );

		$data[] = array( $group, '1.html' );
		return $data;
	}

	// 	public function testForm()
	// 	{
	// 		$form = new Miao_Form( 'frm_test' );
	// 		$attributes = array();
	// 		$title = new Miao_Form_Control_Text( 'title', $attributes );
	// 		$url = new Miao_Form_Control_Text( 'url', $attributes );
	// 		$form->addGroup( 'group', array( $title, $url ) );


	// 		$expected = $form->group->render();


	// 		$this->assertTrue( false );
	// 	}


	/**
	 * @dataProvider providerTestLoad
	 * @param unknown_type $group
	 * @param unknown_type $actualData
	 * @param unknown_type $excptionName
	 */
	public function testLoad( Miao_Form_Control_Group $group, $data, $actual, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}
		$group->setValue( $data );
		$expected = $group->getValue();
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestLoad()
	{
		$data = array();

		$attributes = array();
		$title = new Miao_Form_Control_Text( 'title', $attributes );
		$url = new Miao_Form_Control_Text( 'url', $attributes );

		$group = new Miao_Form_Control_Group( 'group' );
		$group->addControl( $title );
		$group->addControl( $url );

		$formData = array(
			'title' => array( 0 => '1', 1 => '2', 2 => '3' ),
			'url' => array( 0 => 'a', 1 => 'b', 2 => 'c' ) );

		$data[] = array( $group, $formData, $formData );

		$formData = array(
			'title' => array( 0 => '1', 1 => '2', 2 => '3' ),
			'url' => array( 0 => 'a', 1 => 'b' ) );
		$actualFormData = $formData;
		$actualFormData[ 'url' ][ 2 ] = null;
		//$data[] = array( $group, $formData, $actualFormData );

		return $data;
	}

	// 	public function testValidate()
	// 	{


	// 	}
	protected function _clearSpace( $string )
	{
		$search = array( "\r", "\n", "\t", " " );
		$replace = array( '', '', '', '' );
		$result = str_replace( $search, $replace, $string );
		return $result;
	}
}