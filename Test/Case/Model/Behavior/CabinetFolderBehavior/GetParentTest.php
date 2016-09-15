<?php
/**
 * CabinetFolderBehavior::getParent()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('CabinetFileFixture', 'Cabinets.Test/Fixture');

/**
 * CabinetFolderBehavior::getParent()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Model\Behavior\CabinetFolderBehavior
 */
class CabinetFolderBehaviorGetParentTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet',
		'plugin.cabinets.cabinet_file',
		'plugin.cabinets.cabinet_file_tree',
		//'plugin.cabinets.block_setting_for_cabinet',
		//'plugin.workflow.workflow_comment',

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
		//$this->TestModel = ClassRegistry::init('TestCabinets.TestCabinetFolderBehaviorModel');
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
		$records = (new CabinetFileFixture())->records;

		$records = Hash::combine($records, '{n}.id', '{n}');
		$result = array();
		// CabinetFild.id:14の上フォルダはCabinetFile.id:12
		// 親のTreeIdでの親は15
		$result[] = [
			'data' => [
				'CabinetFile' => $records[14],
				'CabinetFileTree' => [
					'parent_id' => 15,
				],
			],
			'result' => 12,
		];
		return $result;
	}

/**
 * getParent()のテスト
 *
 * @param array $cabinetFile cabinetFile data
 * @param array $expects 親フォルダのCabinetFile.id
 * @dataProvider dataProvider
 * @return void
 */
	public function testGetParent($cabinetFile, $expects) {
		//テスト実施
		$result = $this->CabinetFile->getParent($cabinetFile);

		$this->assertEquals($expects, $result['CabinetFile']['id']);
	}

}
