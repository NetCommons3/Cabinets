<?php
/**
 * CabinetFile Test Case
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('CabinetFile', 'Cabinets.Model');

CakePlugin::load('NetCommons');
App::uses('NetCommonsBlockComponent', 'NetCommons.Controller/Component');
App::uses('TestingWrapper', 'Cabinets.Test');

/**
 * Summary for CabinetFile Test Case
 */
class CabinetFileFindTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet_file',
		'plugin.categories.category',
		'plugin.categories.category_order',
		//'plugin.tags.tag',
		//'plugin.tags.tags_content',
		'plugin.users.user', // Trackableビヘイビアでテーブルが必用
		'plugin.workflow.workflow_comment',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->CabinetFile = ClassRegistry::init('Cabinets.CabinetFile');
		// モデルからビヘイビアをはずす:
		$this->CabinetFile->Behaviors->unload('Tag');
		$this->CabinetFile->Behaviors->unload('Trackable');
		$this->CabinetFile->Behaviors->unload('Like');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CabinetFile);

		parent::tearDown();
	}

/**
 * test _getPublishedConditions
 *
 * @return void
 */
	public function testGetPublishedConditions() {
		$CabinetFileTesting = new TestingWrapper($this->CabinetFile);
		$now = '2015-01-01 00:00:00';
		$conditions = $CabinetFileTesting->_testing__getPublishedConditions($now);
		$this->assertEquals(1, $conditions['CabinetFile.is_active']);
		$this->assertEquals($now, $conditions['CabinetFile.publish_start <=']);
	}

/**
 * test getCondition
 *
 * @return void
 */
	public function testGetCondition() {
		$userId = 1;
		$blockId = 2;
		$currentDateTime = '2015-01-01 00:00:00';
		// contentReadable false
		$permissions = array(
			'contentReadable' => false,
			'contentCreatable' => false,
			'contentEditable' => false,
		);
		$conditions = $this->CabinetFile->getConditions(
			$blockId,
			$userId,
			$permissions,
			$currentDateTime
		);
		$this->assertSame(
			$conditions,
			array(
				'CabinetFile.block_id' => $blockId,
				'CabinetFile.id' => 0
			)
		);

		// contentReadable のみ
		$permissions = array(
			'contentReadable' => true,
			'contentCreatable' => false,
			'contentEditable' => false,
		);
		$conditions = $this->CabinetFile->getConditions($blockId, $userId, $permissions, $currentDateTime);
		$this->assertSame(
			$conditions,
			array(
				'CabinetFile.block_id' => $blockId,
				'CabinetFile.is_active' => 1,
				'CabinetFile.publish_start <=' => $currentDateTime
			)
		);

		// 作成権限あり
		$permissions = array(
			'contentReadable' => true,
			'contentCreatable' => true,
			'contentEditable' => false,
		);
		$conditions = $this->CabinetFile->getConditions($blockId, $userId, $permissions, $currentDateTime);
		$this->assertSame(
			$conditions,
			array(
				'CabinetFile.block_id' => $blockId,
				'OR' => array(
					array(
						'CabinetFile.is_active' => 1,
						'CabinetFile.publish_start <=' => $currentDateTime,
						'CabinetFile.created_user !=' => $userId,
					),
					array(
						'CabinetFile.created_user' => $userId,
						'CabinetFile.is_latest' => 1,
					)
				)
			)
		);

		// 編集権限あり
		$permissions = array(
			'contentReadable' => true,
			'contentCreatable' => true,
			'contentEditable' => true,
		);
		$conditions = $this->CabinetFile->getConditions($blockId, $userId, $permissions, $currentDateTime);
		$this->assertSame(
			$conditions,
			array(
				'CabinetFile.block_id' => $blockId,
				'CabinetFile.is_latest' => 1,
			)
		);
	}

/**
 * test getYearMonth
 *
 * @return void
 */
	public function testGetYearMonthCount() {
		$blockId = 5;
		$userId = 1;
		$permissions = array(
			'contentCreatable' => true,
			'contentEditable' => true,
		);
		$currentDateTime = '2015-06-30 00:00:00';
		$counts = $this->CabinetFile->getYearMonthCount($blockId, $userId, $permissions, $currentDateTime);

		$this->assertEquals(1, $counts['2014-02']);
		$this->assertEquals(0, $counts['2014-03']);

		// ファイルがひとつもないケース
		$blockId = 6;
		$counts = $this->CabinetFile->getYearMonthCount($blockId, $userId, $permissions, $currentDateTime);
		$this->assertEquals(1, count($counts));
		$this->assertEquals(0, $counts['2015-06']);
	}

/**
 * 一度も公開になってないかを返すテスト
 *
 * @return void
 */
	public function testYetPublish() {
		$yetPublishFile = $this->CabinetFile->findById(5);
		$resultTrue = $this->CabinetFile->yetPublish($yetPublishFile);
		$this->assertTrue($resultTrue);

		$PublishedFile = $this->CabinetFile->findById(2);
		$resultFalse = $this->CabinetFile->yetPublish($PublishedFile);
		$this->assertFalse($resultFalse);
	}

	//public function testExecuteConditions() {
	//	$userId = 1;
	//	$blockId = 2;
	//	$currentDateTime = '2015-01-01 00:00:00';
	//
	//	// contentReadable false
	//	$permissions = array(
	//		'contentReadable' => false,
	//		'contentCreatable' => false,
	//		'contentEditable' => false,
	//	);
	//	$conditions = $this->CabinetFile->getConditions(
	//		$blockId,
	//		$userId,
	//		$permissions,
	//		$currentDateTime
	//	);
	//
	//	$result = $this->CabinetFile->find('all', array('conditions' => $conditions));
	//	$this->assertSame($result, array());
	//
	//	// contentReadable true
	//	$permissions = array(
	//		'contentReadable' => true,
	//		'contentCreatable' => false,
	//		'contentEditable' => false,
	//	);
	//	$conditions = $this->CabinetFile->getConditions(
	//		$blockId,
	//		$userId,
	//		$permissions,
	//		$currentDateTime
	//	);
	//
	//	$cabinetFiles = $this->CabinetFile->find('all', array('conditions' => $conditions));
	//	$this->assertEqual($cabinetFiles[0]['CabinetFile']['id'], 1);
	//
	//	$publishedFileIs1 = $this->CabinetFile->find('count', array('conditions' => $conditions));
	//
	//	$this->assertEqual($publishedFileIs1, 1);
	//
	//}
	//
	//public function testFind4CreatableUser() {
	//	$userId = 1;
	//	$blockId = 2;
	//	$currentDateTime = '2015-01-01 00:00:00';
	//
	//	// contentCreatable true
	//	$permissions = array(
	//		'contentReadable' => true,
	//		'contentCreatable' => true,
	//		'contentEditable' => false,
	//	);
	//	$conditions = $this->CabinetFile->getConditions(
	//		$blockId,
	//		$userId,
	//		$permissions,
	//		$currentDateTime
	//	);
	//
	//	$cabinetFiles = $this->CabinetFile->find('all', array('conditions' => $conditions));
	//
	//	$publishedAndMyFilesAre3 = $this->CabinetFile->find('count', array('conditions' => $conditions));
	//
	//	$this->assertEqual($publishedAndMyFilesAre3, 3);
	//
	//}
	//
	//public function testFind4EditableUser() {
	//	$userId = 1;
	//	$blockId = 2;
	//	$currentDateTime = '2015-01-01 00:00:00';
	//
	//	// contentCreatable true
	//	$permissions = array(
	//		'contentReadable' => true,
	//		'contentCreatable' => true,
	//		'contentEditable' => true,
	//	);
	//	$conditions = $this->CabinetFile->getConditions(
	//		$blockId,
	//		$userId,
	//		$permissions,
	//		$currentDateTime
	//	);
	//
	//	$cabinetFiles = $this->CabinetFile->find('all', array('conditions' => $conditions));
	//
	//	$filesAre4 = $this->CabinetFile->find('count', array('conditions' => $conditions));
	//
	//	$this->assertEqual($filesAre4, 4);
	//
	//}
}
