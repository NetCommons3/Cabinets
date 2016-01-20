<?php
/**
 * CabinetAppModel Test Case
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('CabinetsAppModel', 'Cabinets.Model');
App::uses('CabinetsAppModelTestBase', 'Cabinets.Test/Case/Model');
App::uses('TestingWrapper', 'Cabinets.Test');

/**
 * Class CabinetFakeModel テスト用Fakeモデル
 */
class CabinetFakeModel extends CabinetsAppModel {

/**
 * @var bool Fakeなのでテーブル使わない
 */
	public $useTable = false;
}

/**
 * Summary for CabinetAppModel Test Case
 */
class CabinetsAppModelTest extends CabinetsAppModelTestBase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet_file',
		'plugin.categories.category',
		'plugin.categories.category_order',
		////'plugin.tags.tag',
		////'plugin.tags.tags_content',
		//'plugin.users.user', // Trackableビヘイビアでテーブルが必用
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		//$this->CabinetAppModel = ClassRegistry::init('Cabinets.CabinetAppModel');
		$this->CabinetFile = ClassRegistry::init('Cabinets.CabinetFile');
		$this->_unloadTrackable($this->CabinetFile);
		$this->CabinetFile->Behaviors->unload('Tag');
		$this->CabinetFile->Behaviors->unload('Like');
		$this->CabinetFile->Behaviors->unload('Category');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CabinetAppModel);
		unset($this->CabinetFile);

		parent::tearDown();
	}

/**
 * test getNew
 *
 * @return void
 */
	public function testGetNew() {
		$new = $this->CabinetFile->getnew();

		$this->assertInternalType('array', $new);
	}

/**
 * test tarnsaction method
 *
 * @return void
 */
	public function testTransaction() {
		$this->CabinetFile->begin();

		$result = $this->_saveOneData();

		$savedData = $this->CabinetFile->findById($result['CabinetFile']['id']);
		$this->assertEquals('title', $savedData['CabinetFile']['title']);
		$this->CabinetFile->rollback();

		$savedDataNotFound = $this->CabinetFile->findById($result['CabinetFile']['id']);
		$this->assertEmpty($savedDataNotFound);

		$this->CabinetFile->begin();
		$result = $this->_saveOneData();
		$this->CabinetFile->commit();
		$savedDataFound = $this->CabinetFile->findById($result['CabinetFile']['id']);
		$this->assertEquals('title', $savedDataFound['CabinetFile']['title']);
	}

/**
 * _getValidateSpecificationテスト
 *
 * @return void
 */
	public function testGetValidateSpecification() {
		$CabinetFakeModel = new CabinetFakeModel();
		$CabinetFileTesting = new TestingWrapper($CabinetFakeModel);
		$specificationArray = $CabinetFileTesting->_testing__getValidateSpecification();
		$this->assertInternalType('array', $specificationArray);
	}

/**
 * testTransaction用にデータ保存する
 *
 * @return mixed
 */
	protected function _saveOneData() {
		$data = $this->CabinetFile->getNew();
		$data['CabinetFile']['title'] = 'title';
		$data['CabinetFile']['body1'] = 'body1text';
		$data['CabinetFile']['status'] = 2;
		$data['CabinetFile']['key'] = 1;
		$data['CabinetFile']['language_id'] = 1;
		$data['CabinetFile']['block_id'] = 1;
		$data['CabinetFile']['cabinet_key'] = 'cabinet1';
		$result = $this->CabinetFile->save($data);
		return $result;
	}
}
