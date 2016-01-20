<?php
/**
 * All Test
 */
/**
 * CabinetFilesEdit All Test Suite
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case
 * @codeCoverageIgnore
 */
class AllCabinetFilesEditControllerTest extends CakeTestSuite {

/**
 * All test suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$contoller = preg_replace('/^All([\w]+)ControllerTest$/', '$1', __CLASS__);

		$suite = new CakeTestSuite(sprintf('All %s Controller tests', $contoller));
		$path = __DIR__;
		$suite->addTestDirectoryRecursive($path);

		return $suite;
	}
}
