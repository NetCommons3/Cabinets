<?php
/**
 * CabinetFolderBehavior::hasChildren()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('CabinetFileFixture', 'Cabinets.Test/Fixture');
App::uses('CabinetFileTreeFixture', 'Cabinets.Test/Fixture');

/**
 * CabinetFolderBehavior::hasChildren()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Model\Behavior\CabinetFolderBehavior
 */
class CabinetFolderBehaviorHasChildrenTest extends NetCommonsModelTestCase {

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
		// +- id 11 content_key_11  tree.id 11
		//   +- id 12	content_key_12	tree.id 15
		//     +- id 14	content_key_14	tree.id 17
		//       +- id 13 content_key_13 tree.id 16
		// +- id 10 root 下位フォルダ無し
		$records = (new CabinetFileFixture())->records;
		$treeRecords = (new CabinetFileTreeFixture())->records;

		$records = Hash::combine($records, '{n}.id', '{n}');
		$treeRecords = Hash::combine($treeRecords, '{n}.id', '{n}');
		$result = array();
		// CabinetFile.id:14の上フォルダはCabinetFile.id:12
		// 親のTreeIdでの親は15
		$result[] = [
			'data' => [
				'Cabinet' => [
					'key' => 'content_block_1'
				],
				'CabinetFile' => $records[14],
				'CabinetFileTree' => $treeRecords[17],
			],
			'expects' => true,
		];
		$result[] = [
			'data' => [
				'Cabinet' => [
					'key' => 'cabinet_3'
				],
				'CabinetFile' => $records[10],
				'CabinetFileTree' => $treeRecords[10],
			],
			'expects' => false,
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
	public function testHasChildren($cabinetFile, $expects) {
		//テスト実施
		$result = $this->CabinetFile->hasChildren($cabinetFile);

		$this->assertEquals($expects, $result);
	}

}
