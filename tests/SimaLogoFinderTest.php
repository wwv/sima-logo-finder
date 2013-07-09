<?php
require_once 'bootstrap.php';

class SimaLogoFinderTest extends CTestCase
{

	function testMain()
	{
		$dir = dirname(__FILE__);

		foreach (glob($dir . '/old-logo/*.jpg') as $file)
			$this->assertTrue(SimaLogoFinder::hasOldLogo($file));

		foreach (glob($dir . '/new-logo/*.jpg') as $file)
			$this->assertFalse(SimaLogoFinder::hasOldLogo($file));
	}

}