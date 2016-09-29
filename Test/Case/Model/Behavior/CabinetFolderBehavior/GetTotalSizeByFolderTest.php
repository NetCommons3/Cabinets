<?php
/**
 * CabinetFolderBehavior::getTotalSizeByFolder()のテスト
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
 * CabinetFolderBehavior::getTotalSizeByFolder()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Model\Behavior\CabinetFolderBehavior
 */
class CabinetFolderBehaviorGetTotalSizeByFolderTest extends NetCommonsModelTestCase {

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
 * getTotalSizeByFolder()テストのDataProvider
 *
 * ### 戻り値
 *  - folder CabinetFileデータ
 *
 * @return array データ
 */
	public function dataProvider() {
		// +- id 12	content_key_12	tree.id 15
		//   +- id 14	content_key_14	tree.id 17
		//     +- id 13 content_key_13 tree.id 16
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
			'size' => 1000,
		];
		return $result;
	}

/**
 * getTotalSizeByFolder()のテスト
 *
 * @param array $folder CabinetFileデータ
 * @param int $size 合計サイズ
 * @dataProvider dataProvider
 * @return void
 */
	public function testGetTotalSizeByFolder($folder, $size) {
		//テスト実施
		$result = $this->CabinetFile->getTotalSizeByFolder($folder);

		$this->assertEquals($size, $result);
	}

}
