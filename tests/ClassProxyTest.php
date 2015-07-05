<?php
use wpphpbbu\ClassProxy;

class ClassProxyTest extends \WP_UnitTestCase
{

	public function testCacheIsCreated()
	{
	    $foo = true;
	    $proxy = new ClassProxy();
		$proxy->setCache();
		$this->assertFileExists(__DIR__.'\..\cache\common.php');
		
	}
	
	public function testCacheDirectoryIsEmpty()
	{
	    $foo = true;
	    $proxy = new ClassProxy();
		$proxy->cleanFolder();
		$this->assertFileNotExists(__DIR__.'\..\cache\common.php');
		
	}

}

