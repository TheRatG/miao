<?php
class Miao_Form_Control_List_Test extends PHPUnit_Framework_TestCase
{
	public function testText()
	{
		$name = 'name';
		$attributes = array();
		$value = array( 'a', 'b' );

		$control = new Miao_Form_Control_Text( $name, $attributes );

		$list = new Miao_Form_Control_List( $control );
        $list->setLabel( 'Label' )
            ->setAttributes( $attributes );
		$list->setValue( $value );
		$expected = $list->render();

		$actual = '<input name="name[]" value="a" type="text" /><input name="name[]" value="b" type="text" />';
		$this->assertEquals( $expected, $actual );

		$actual = '<input name="name[]" value="a" type="text" />';
		$expected = $list[0]->render();
		$this->assertEquals( $expected, $actual );

        $expected = count( $list );
        $actual = 2;
        $this->assertEquals( $expected, $actual );
	}
}