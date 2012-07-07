<?php
class Miao_Console_Test extends PHPUnit_Framework_TestCase
{
	static public function setUpBeforeClass()
	{
	}

	static public function tearDownAfterClass()
	{
	}


	public function setUp()
	{
		$this->_createFolders( array(
			'Miao_TestModuleForCreate'
			, 'Miao_TestModuleForCreateOffice2_View_Brain'
		) );

		$this->_createFolders( array(
			'Miao_TestModuleForDelete1'
			, 'Miao_TestModuleForDelete2_Check'
		) );

		$this->_createFolders( array(
			'Miao_TestModuleForCopy'
			, 'Miao_TestModuleForCopy3'
			, 'Miao_TestModuleForCopy4'
			, 'Miao_TestModuleForCopy6_Test'
			, 'Miao_TestModuleForCopy7_Test'
			, 'Miao_TestModuleForCopy8'
			, 'Miao_TestModuleForCopy9_Test'
			, 'Miao_TestModuleForCopy10_Test'
			, 'Miao_TestModuleForCopy11_Test'
			, 'Miao_TestModuleForCopy12'
			, 'Miao_TestModuleForCopy14_Test'
			, 'Miao_TestModuleForCopy15'
		) );

		$this->_createFolders( array(
			'Miao_TestModuleForRename'
			, 'Miao_TestModuleForRename3'
			, 'Miao_TestModuleForRename4'
			, 'Miao_TestModuleForRename6'
			, 'Miao_TestModuleForRename7_Test'
			, 'Miao_TestModuleForRename8_Test'
			, 'Miao_TestModuleForRename9'
			, 'Miao_TestModuleForRename10'
			, 'Miao_TestModuleForRename11_Test'
			, 'Miao_TestModuleForRename12_Test'
			, 'Miao_TestModuleForRename13'
			, 'Miao_TestModuleForRename15_Test'
			, 'Miao_TestModuleForRename16_Test'
		) );
	}

	public function tearDown()
	{
		$this->_deleteFolders( array(
			'Miao_TestModuleForCreate'
			, 'Miao_TestModuleForCreateOffice'
			, 'Miao_TestModuleForCreateOffice2_View_Brain'
			, 'Miao_TestModuleForCreateOffice2_ViewBlock_Brain'
			, 'Miao_TestModuleForCreateOffice2_Action_Brain'
			, 'Miao_TestModuleForCreate10_Test'
		) );

		$this->_deleteFolders( array(
			'Miao_TestModuleForDelete1'
			, 'Miao_TestModuleForDelete2_Check'
			, 'Miao_TestModuleForDelete3_Check_Back'
			, 'Miao_TestModuleForDelete4'
		) );

		$this->_deleteFolders( array(
			'Miao_TestModuleForCopy'
			, 'Miao_TestModuleForCopy1'
			, 'Miao_TestModuleForCopy2'
			, 'Miao_TestModuleForCopy3'
			, 'Miao_TestModuleForCopy4'
			, 'Miao_TestModuleForCopy5'
			, 'Miao_TestModuleForCopy6'
			, 'Miao_TestModuleForCopy7'
			, 'Miao_TestModuleForCopy8'
			, 'Miao_TestModuleForCopy9'
			, 'Miao_TestModuleForCopy10'
			, 'Miao_TestModuleForCopy11'
			, 'Miao_TestModuleForCopy12'
			, 'Miao_TestModuleForCopy13'
			, 'Miao_TestModuleForCopy14'
			, 'Miao_TestModuleForCopy15'
		) );

		$this->_deleteFolders( array(
			'Miao_TestModuleForRename'
			, 'Miao_TestModuleForRename1'
			, 'Miao_TestModuleForRename2'
			, 'Miao_TestModuleForRename3'
			, 'Miao_TestModuleForRename4'
			, 'Miao_TestModuleForRename5'
			, 'Miao_TestModuleForRename6'
			, 'Miao_TestModuleForRename7'
			, 'Miao_TestModuleForRename8'
			, 'Miao_TestModuleForRename9'
			, 'Miao_TestModuleForRename10'
			, 'Miao_TestModuleForRename11'
			, 'Miao_TestModuleForRename12'
			, 'Miao_TestModuleForRename13'
			, 'Miao_TestModuleForRename14'
			, 'Miao_TestModuleForRename15'
			, 'Miao_TestModuleForRename16'
		) );
	}


	/**
	 * @dataProvider providerTestAdd
	 */
	public function testAdd( $className, $isModule = false, $expectedException = '' )
	{
		if ( !empty( $expectedException ) )
		{
			$this->setExpectedException( $expectedException );
		}

		$console = new Miao_Console( $className, 'TESTER' );
		$console->add();

		$status = true;
		if ( !$isModule )
		{
			$path = Miao_Path::getDefaultInstance()->getFilenameByClassName( $className );
			if ( !file_exists( $path ) )
			{
				$status = false;
			}
		}
		else
		{
			$path = Miao_Path::getDefaultInstance()->getModuleRoot( $className );
			if ( !is_dir( $path )
				|| !is_dir( sprintf( '%s/data', $path ) )
				|| !is_dir( sprintf( '%s/classes', $path ) )
				|| !is_dir( sprintf( '%s/tests/classes', $path ) )
				|| !is_dir( sprintf( '%s/tests/sources', $path ) ) )
			{
				$status = false;
			}
		}

		$this->assertTrue( $status );
	}

	public function providerTestAdd()
	{
		$data = array();
		$data[] = array(
			'className' => 'UnusedTestApp'
			, 'isModule' => false
			, 'expectedException' => 'Miao_Console_Exception_LibNotFound'
		);
		$data[] = array(
			'className' => 'UnusedTestApp_Mode'
			, 'isModule' => true
			, 'expectedException' => 'Miao_Console_Exception_LibNotFound'
		);
		$data[] = array(
			'className' => 'UnusedTestApp_Mode_Eval'
			, 'isModule' => false
			, 'expectedException' => 'Miao_Console_Exception_LibNotFound'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForCreate'
			, 'isModule' => true
			, 'expectedException' => 'Miao_Console_Exception_ModuleExists'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForCreateOffice'
			, 'isModule' => true
			, 'expectedException' => ''
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForCreateOffice2_View_Brain'
			, 'isModule' => false
			, 'expectedException' => 'Miao_Console_Exception_ClassExists'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForCreateOffice2_ViewBlock_Brain'
			, 'isModule' => false
			, 'expectedException' => ''
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForCreateOffice2_Action_Brain'
			, 'isModule' => false
			, 'expectedException' => ''
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForCreate10_Test'
			, 'isModule' => false
			, 'expectedException' => 'Miao_Console_Exception_ModuleNotFound'
		);
		$data[] = array(
			'className' => 'PHPUnit_Test'
			, 'isModule' => true
			, 'expectedException' => 'Miao_Console_Exception_WrongLibType'
		);

		return $data;
	}


	/**
	 * @dataProvider providerTestDel
	 */
	public function testDel( $className, $isModule = false, $expectedException = '' )
	{
		if ( !empty( $expectedException ) )
		{
			$this->setExpectedException( $expectedException );
		}

		$console = new Miao_Console( $className, 'TESTER' );
		$console->del();

		$status = true;
		if ( !$isModule )
		{
			$path = Miao_Path::getDefaultInstance()->getFilenameByClassName( $className );
			if ( file_exists( $path ) )
			{
				$status = false;
			}
		}
		else
		{
			$path = Miao_Path::getDefaultInstance()->getModuleRoot( $className );
			if ( is_dir( $path ) )
			{
				$status = false;
			}
		}

		$this->assertTrue( $status );
	}

	public function providerTestDel()
	{
		$data = array();
		$data[] = array(
			'className' => 'UnusedTestApp'
			, 'isModule' => false
			, 'expectedException' => 'Miao_Console_Exception_LibNotFound'
		);
		$data[] = array(
			'className' => 'UnusedTestApp_Mode'
			, 'isModule' => true
			, 'expectedException' => 'Miao_Console_Exception_LibNotFound'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForDelete1'
			, 'isModule' => true
			, 'expectedException' => ''
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForDelete2_Check'
			, 'isModule' => false
			, 'expectedException' => ''
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForDelete3_Check_Back'
			, 'isModule' => false
			, 'expectedException' => 'Miao_Console_Exception_ClassNotFound'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForDelete4'
			, 'isModule' => true
			, 'expectedException' => 'Miao_Console_Exception_ModuleNotFound'
		);
		$data[] = array(
			'className' => 'PHPUnit_Test'
			, 'isModule' => true
			, 'expectedException' => 'Miao_Console_Exception_WrongLibType'
		);

		return $data;
	}


	/**
	 * @dataProvider providerTestCp
	 */
	public function testCp( $className, $newClassName, $isModule = false, $expectedException = '' )
	{
		if ( !empty( $expectedException ) )
		{
			$this->setExpectedException( $expectedException );
		}

		$console = new Miao_Console( $className, 'TESTER' );
		$console->cp( $newClassName );

		$status = true;
		if ( !$isModule )
		{
			$newPath = Miao_Path::getDefaultInstance()->getFilenameByClassName( $newClassName );
			if ( !file_exists( $newPath ) )
			{
				$status = false;
			}
			else
			{
				$content = file_get_contents( $newPath );
				if ( !$content )
				{
					$status = false;
				}
				else
				{
					if ( false !== stripos( $content, $className ) )
					{
						$status = false;
					}
				}
			}
		}
		else
		{
			$newPath = Miao_Path::getDefaultInstance()->getModuleRoot( $newClassName );
			if ( !is_dir( $newPath ) )
			{
				$status = false;
			}
			else
			{
				$classPath = Miao_Path::getDefaultInstance()->getFilenameByClassName( $newClassName );
				$content = file_get_contents( $classPath );
				if ( !$content )
				{
					$status = false;
				}
				else
				{
					if ( false !== stripos( $content, $className ) )
					{
						$status = false;
					}
				}
			}
		}

		$this->assertTrue( $status );
	}

	public function providerTestCp()
	{
		$data = array();
		$data[] = array(
			'className' => 'UnusedTestApp'
			, 'newClassName' => 'UnusedTestApp2'
			, 'isModule' => false
			, 'expectedException' => 'Miao_Console_Exception_LibNotFound'
		);
		$data[] = array(
			'className' => 'UnusedTestApp_Mode'
			, 'newClassName' => 'UnusedTestApp_Mode2'
			, 'isModule' => true
			, 'expectedException' => 'Miao_Console_Exception_LibNotFound'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForCopy'
			, 'newClassName' => 'UnusedTestApp_Mode2'
			, 'isModule' => true
			, 'expectedException' => 'Miao_Console_Exception_LibNotFound'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForCopy1'
			, 'newClassName' => 'Miao_TestModuleForCopy2'
			, 'isModule' => true
			, 'expectedException' => 'Miao_Console_Exception_ModuleNotFound'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForCopy3'
			, 'newClassName' => 'Miao_TestModuleForCopy4'
			, 'isModule' => true
			, 'expectedException' => 'Miao_Console_Exception_ModuleExists'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForCopy5_Test'
			, 'newClassName' => 'Miao_TestModuleForCopy6_Test'
			, 'isModule' => false
			, 'expectedException' => 'Miao_Console_Exception_ClassNotFound'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForCopy6_Test'
			, 'newClassName' => 'Miao_TestModuleForCopy7_Test'
			, 'isModule' => false
			, 'expectedException' => 'Miao_Console_Exception_ClassExists'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForCopy8'
			, 'newClassName' => 'Miao_TestModuleForCopy9_Test'
			, 'isModule' => true
			, 'expectedException' => 'Miao_Console_Exception_DifferentLevels'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForCopy10_Test'
			, 'newClassName' => 'Miao_TestModuleForCopy11_Test_Test'
			, 'isModule' => false
			, 'expectedException' => 'Miao_Console_Exception_DifferentLevels'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForCopy12'
			, 'newClassName' => 'Miao_TestModuleForCopy13'
			, 'isModule' => true
			, 'expectedException' => ''
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForCopy14_Test'
			, 'newClassName' => 'Miao_TestModuleForCopy15_NewTest'
			, 'isModule' => false
			, 'expectedException' => ''
		);

		return $data;
	}


	/**
	 * @dataProvider providerTestRen
	 */
	public function testRen( $className, $newClassName, $isModule = false, $expectedException = '' )
	{
		if ( !empty( $expectedException ) )
		{
			$this->setExpectedException( $expectedException );
		}

		$console = new Miao_Console( $className, 'TESTER' );
		$console->ren( $newClassName );

		$status = true;
		if ( !$isModule )
		{
			$oldModulePath = Miao_Path::getDefaultInstance()->getModuleRoot( $className );
			$oldPath = Miao_Path::getDefaultInstance()->getFilenameByClassName( $className );
			$newPath = Miao_Path::getDefaultInstance()->getFilenameByClassName( $newClassName );
			if ( !is_dir( $oldModulePath ) || !file_exists( $newPath ) || file_exists( $oldPath ) )
			{
				$status = false;
			}
			else
			{
				$content = file_get_contents( $newPath );
				if ( !$content )
				{
					$status = false;
				}
				else
				{
					if ( false !== stripos( $content, $className ) )
					{
						$status = false;
					}
				}
			}
		}
		else
		{
			$oldPath = Miao_Path::getDefaultInstance()->getModuleRoot( $className );
			$newPath = Miao_Path::getDefaultInstance()->getModuleRoot( $newClassName );
			if ( !is_dir( $newPath ) || is_dir( $oldPath ) )
			{
				$status = false;
			}
			else
			{
				$classPath = Miao_Path::getDefaultInstance()->getFilenameByClassName( $newClassName );
				$content = file_get_contents( $classPath );
				if ( !$content )
				{
					$status = false;
				}
				else
				{
					if ( false !== stripos( $content, $className ) )
					{
						$status = false;
					}
				}
			}
		}

		$this->assertTrue( $status );
	}

	public function providerTestRen()
	{
		$data = array();
		$data[] = array(
			'className' => 'UnusedTestApp'
			, 'newClassName' => 'UnusedTestApp2'
			, 'isModule' => false
			, 'expectedException' => 'Miao_Console_Exception_LibNotFound'
		);
		$data[] = array(
			'className' => 'UnusedTestApp_Mode'
			, 'newClassName' => 'UnusedTestApp_Mode2'
			, 'isModule' => true
			, 'expectedException' => 'Miao_Console_Exception_LibNotFound'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForRename'
			, 'newClassName' => 'UnusedTestApp_Mode2'
			, 'isModule' => true
			, 'expectedException' => 'Miao_Console_Exception_LibNotFound'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForRename1'
			, 'newClassName' => 'Miao_TestModuleForRename2'
			, 'isModule' => true
			, 'expectedException' => 'Miao_Console_Exception_ModuleNotFound'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForRename3'
			, 'newClassName' => 'Miao_TestModuleForRename4'
			, 'isModule' => true
			, 'expectedException' => 'Miao_Console_Exception_ModuleExists'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForRename5_Test'
			, 'newClassName' => 'Miao_TestModuleForRename6_Test'
			, 'isModule' => false
			, 'expectedException' => 'Miao_Console_Exception_ClassNotFound'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForRename7_Test'
			, 'newClassName' => 'Miao_TestModuleForRename8_Test'
			, 'isModule' => false
			, 'expectedException' => 'Miao_Console_Exception_ClassExists'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForRename9'
			, 'newClassName' => 'Miao_TestModuleForRename10_Test'
			, 'isModule' => true
			, 'expectedException' => 'Miao_Console_Exception_DifferentLevels'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForRename11_Test'
			, 'newClassName' => 'Miao_TestModuleForRename12_Test_Test'
			, 'isModule' => false
			, 'expectedException' => 'Miao_Console_Exception_DifferentLevels'
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForRename13'
			, 'newClassName' => 'Miao_TestModuleForRename14'
			, 'isModule' => true
			, 'expectedException' => ''
		);
		$data[] = array(
			'className' => 'Miao_TestModuleForRename15_Test'
			, 'newClassName' => 'Miao_TestModuleForRename16_NewTest'
			, 'isModule' => false
			, 'expectedException' => ''
		);

		return $data;
	}



	protected function _createFolders( array $list )
	{
		foreach ( $list as $className )
		{
			try
			{
				$path = Miao_Path::getDefaultInstance()->getFilenameByClassName( $className );
				Miao_Console_Helper::mkFile( $path, $className );
			}
			catch ( Exception $ex )
			{
			}
		}
	}

	protected function _deleteFolders( array $list )
	{
		foreach ( $list as $className )
		{
			try
			{
				$path = Miao_Path::getDefaultInstance()->getModuleRoot( $className );
				Miao_PHPUnit::rmdirr( $path );
			}
			catch ( Exception $ex )
			{
			}
		}
	}
}