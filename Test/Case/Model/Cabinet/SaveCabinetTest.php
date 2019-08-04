<?php
/**
 * Cabinet::saveCabinet()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsSaveTest', 'NetCommons.TestSuite');
App::uses('CabinetFixture', 'Cabinets.Test/Fixture');

/**
 * Cabinet::saveCabinet()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Model\Cabinet
 */
class CabinetSaveCabinetTest extends NetCommonsSaveTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet',
		'plugin.cabinets.cabinet_file',
		'plugin.cabinets.cabinet_file_tree',
		'plugin.cabinets.block_setting_for_cabinet',
		'plugin.categories.category',
		'plugin.categories.category_order',
		'plugin.workflow.workflow_comment',

		'plugin.authorization_keys.authorization_keys',
		'plugin.site_manager.site_setting',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'cabinets';

/**
 * Model name
 *
 * @var string
 */
	protected $_modelName = 'Cabinet';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		Current::$current['Block']['id'] = '2';
		Current::$current['Room']['id'] = '1';
		Current::writePermission('1', 'content_editable', true);
		Current::writePermission('1', 'content_publishable', true);
	}

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'saveCabinet';

/**
 * test makeRootFolder
 *
 * @return void
 */
	public function testMakeRootFolder() {
		// キャビネットを新規登録するとルートフォルダができる
		$data['Cabinet'] = (new CabinetFixture())->records[0];
		unset($data['Cabinet']['id']);
		unset($data['Cabinet']['key']);

		$result = $this->Cabinet->saveCabinet($data);
		$this->assertTrue($result);

		$newCabinet = $this->Cabinet->findById($this->Cabinet->id);
		$CabinetFile = ClassRegistry::init('Cabinets.CabinetFile');
		// rootフォルダができる
		$cabinetKey = $newCabinet['Cabinet']['key'];
		$rootFolder = $CabinetFile->find(
			'first',
			[
				'conditions' => [
					'CabinetFile.cabinet_key' => $cabinetKey,
					'CabinetFileTree.parent_id' => null,
				],
				'recursive' => 0,
			]
		);
		$this->assertNotEmpty($rootFolder);

		// 同名でルートフォルダは作成される
		$this->assertEquals($rootFolder['CabinetFile']['filename'], $newCabinet['Cabinet']['name']);
	}

/**
 * test syncRootFolder
 *
 * @return void
 */
	public function testSyncRootFolder() {
		// キャビネットを新規登録するとルートフォルダができる
		$data['Cabinet'] = (new CabinetFixture())->records[0];
		$data['Cabinet']['name'] = 'Edit Cabinet Name';

		$result = $this->Cabinet->saveCabinet($data);
		$this->assertTrue($result);

		$CabinetFile = ClassRegistry::init('Cabinets.CabinetFile');
		// rootフォルダができる
		$cabinetKey = $data['Cabinet']['key'];
		$rootFolder = $CabinetFile->find(
			'first',
			[
				'conditions' => [
					'CabinetFile.cabinet_key' => $cabinetKey,
					'CabinetFileTree.parent_id' => null,
				],
				'recursive' => 0,
			]
		);
		$this->assertNotEmpty($rootFolder);

		// 同名でルートフォルダは作成される
		$this->assertEquals($rootFolder['CabinetFile']['filename'], $data['Cabinet']['name']);
	}

/**
 * Save用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return array テストデータ
 */
	public function dataProviderSave() {
		$data['Cabinet'] = (new CabinetFixture())->records[0];

		// テストパタンを書く
		$results = array();
		// * 編集の登録処理
		$results[0] = array($data);
		// * 新規の登録処理
		$results[1] = array($data);
		$results[1] = Hash::insert($results[1], '0.Cabinet.id', null);
		$results[1] = Hash::insert($results[1], '0.Cabinet.key', null);
		$results[1] = Hash::remove($results[1], '0.Cabinet.created_user');
		$results[1] = Hash::remove($results[1], '0.Cabinet.created');

		return $results;
	}

/**
 * SaveのExceptionError用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return array テストデータ
 */
	public function dataProviderSaveOnExceptionError() {
		$data = $this->dataProviderSave()[0][0];

		// テストパタンを書く
		return array(
			array($data, 'Cabinets.Cabinet', 'save'),
		);
	}

/**
 * SaveのValidationError用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド(省略可：デフォルト validates)
 *
 * @return array テストデータ
 */
	public function dataProviderSaveOnValidationError() {
		$data = $this->dataProviderSave()[0][0];

		// テストパタンを書く
		return array(
			array($data, 'Cabinets.Cabinet'),
		);
	}

}
