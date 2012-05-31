<?php

/**
 * Action_My.php
 * class App_FrontOffice_Action_Object_Save extends Miao_Office_Action_Form
 * {
 * 		function execute()
 * 		{
 * 			$this->setDoFunctionName( array( $this, '_doSmth' ) );
 * 			$this->setRedirectUrl( $url );
 * 		}
 * }
 *
 * $form = new MyForm();
 * $isValid = $form->isValid();
 * if ( $isValid )
 * {
 * 		$this->_doSmth( $form->getValues() );
 * }
 * $this->setRedirectUrl( $url );
 * $form->save();
 */
abstract class Miao_Form_Controller
{
	private $_form;

	public function __construct()
	{

	}

	abstract public function buildForm();
}
