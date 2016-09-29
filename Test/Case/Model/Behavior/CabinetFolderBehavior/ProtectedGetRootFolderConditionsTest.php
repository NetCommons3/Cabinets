<?php
/**
 * CabinetFolderBehavior::_getRootFolderConditions()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('CabinetFolderBehavior', 'Cabinets.Model/Behavior');

/**
 * CabinetFolderBehavior::_getRootFolderConditions()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Model\Behavior\CabinetFolderBehavior
 */
class CabinetFolderBehaviorProtectedGetRootFolderConditionsTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

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
		$this->TestModel = ClassRegistry::init('TestCabinets.TestCabinetFolderBehaviorProtectedModel');
	}

/**
 * _getRootFolderConditions()テストのDataProvider
 *
 * ### 戻り値
 *  - cabinet Cabinetデータ
 *
 * @return array データ
 */
	public function dataProvider() {
		$result[0] = array();
		$result[0]['cabinet'] = [
			'Cabinet' => [
				'key' => 'cabinet_key_1'
			]
		];
		$result[0]['conditions'] = [
			'cabinet_key' =>'cabinet_key_1',
			'parent_id' => null,
		];

		return $result;
	}

/**
 * _getRootFolderConditions()のテスト
 *
 * @param array $cabinet Cabinetデータ
 * @dataProvider dataProvider
 * @return void
 */
	public function testGetRootFolderConditions($cabinet, $conditions) {
		$behavior = new CabinetFolderBehavior();

		//テストデータ

		//テスト実施
		$result = $this->_testReflectionMethod(
			$behavior, '_getRootFolderConditions', array($cabinet)
		);

		$this->assertEquals($conditions, $result);
	}

}
