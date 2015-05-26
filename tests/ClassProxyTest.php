<?php
use wpphpbbu\ClassProxy;


class ClassProxyTest extends \PHPUnit_Framework_TestCase
{

	public function testCacheIsCreated()
	{
	    $foo = true;
	    $proxy = new \ClassProxy();
		$proxy->setCache();
		$this->assertFileExists(__DIR__.'\..\cache\common.php');
		
	}
	
	public function testCacheDirectoryIsEmpty()
	{
	    $foo = true;
	    $proxy = new ClassProxy();
		$proxy->clean_folder();
		$this->assertFileNotExists(__DIR__.'\..\cache\common.php');
		
	}

}

