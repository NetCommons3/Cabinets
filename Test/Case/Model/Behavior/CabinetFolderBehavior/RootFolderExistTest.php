<?php
/**
 * CabinetFolderBehavior::rootFolderExist()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('CabinetFixture', 'Cabinets.Test/Fixture');

/**
 * CabinetFolderBehavior::rootFolderExist()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Model\Behavior\CabinetFolderBehavior
 */
class CabinetFolderBehaviorRootFolderExistTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet',
		'plugin.cabinets.cabinet_file',
		'plugin.cabinets.cabinet_file_tree',

		'plugin.authorization_keys.authorization_keys',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'cabinets';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Cabinets', 'TestCabinets');
		$this->CabinetFile = ClassRegistry::init('Cabinets.CabinetFile');
	}

/**
 * getParent()テストのDataProvider
 *
 * ### 戻り値
 *  - cabinetFile cabinetFile data
 *
 * @return array データ
 */
	public function dataProvider() {
		$cabinets = (new CabinetFixture())->records;
		$cabinets = Hash::combine($cabinets, '{n}.id', '{n}');

		//$cabinetFiles = (new CabinetFileFixture())->records;
		//$cabinetFiles = Hash::combine($cabinetFiles, '{n}.id', '{n}');

		$result = array();

		// ルートフォルダありのキャビネット
		$result[] = [
			'data' => [
				'Cabinet' => $cabinets[3],
			],
			'result' => true,
		];
		// ルートフォルダ無しのキャビネット
		$result[] = [
			'data' => [
				'Cabinet' => $cabinets[4],
			],
			'result' => false,
		];
		return $result;
	}

/**
 * getParent()のテスト
 *
 * @param array $cabinet cabinet data
 * @param bool $exist ルートフォルダ有無
 * @dataProvider dataProvider
 * @return void
 */
	public function testRootFolderExist($cabinet, $exist) {
		//テスト実施
		$result = $this->CabinetFile->rootFolderExist($cabinet);

		$this->assertEquals($exist, $result);
	}

}
