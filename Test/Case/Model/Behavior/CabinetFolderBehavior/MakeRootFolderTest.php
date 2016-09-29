<?php
/**
 * CabinetFolderBehavior::makeRootFolder()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * CabinetFolderBehavior::makeRootFolder()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Model\Behavior\CabinetFolderBehavior
 */
class CabinetFolderBehaviorMakeRootFolderTest extends NetCommonsModelTestCase {

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
		'plugin.workflow.workflow_comment',

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

		Current::$current['Permission']['content_editable']['value'] = true;
		Current::$current['Permission']['content_publishable']['value'] = true;
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
		$result[] = [
			'data' => [
				'Cabinet' => [
					'id' => '5',
					'name' => 'NewCabinet',
					'key' => 'new_cabinet_key'
				],
			],
			'return' => true,
			//'rootFolder' => [
			//	'CabinetFile' => [
			//		'filename' => 'NewCabinet',
			//		'cabinet_id' => 5
			//	],
			//]
		];
		return $result;
	}

/**
 * getParent()のテスト
 *
 * @param array $cabinet cabinetFile data
 * @param array $expects 親フォルダのCabinetFile.id
 * @dataProvider dataProvider
 * @return void
 */
	public function testMakeRootFolder($cabinet, $return) {
		//テスト実施
		$result = $this->CabinetFile->makeRootFolder($cabinet);

		$this->assertEquals($return, $result);

		$savedRootFolder = $this->CabinetFile->getRootFolder($cabinet);
		$this->assertEquals($cabinet['Cabinet']['name'],
			$savedRootFolder['CabinetFile']['filename']);
		$this->assertEquals($cabinet['Cabinet']['id'],
			$savedRootFolder['CabinetFile']['cabinet_id']);
	}

}
