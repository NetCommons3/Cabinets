<?php
/**
 * All Test
 */

/**
 * Cabinets All Test Suite
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case
 * @codeCoverageIgnore
 */
class AllCabinetsTest extends CakeTestSuite {

/**
 * All test suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$plugin = preg_replace('/^All([\w]+)Test$/', '$1', __CLASS__);
		$suite = new CakeTestSuite(sprintf('All %s Plugin tests', $plugin));
		//$suite->addTestDirectoryRecursive(CakePlugin::path($plugin) . 'Test' . DS . 'Case');

		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . DS . 'Model' . DS . 'CabinetDummyTest.php');
		return $suite;
	}
}
