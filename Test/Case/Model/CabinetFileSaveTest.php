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
class CabinetFileSaveTest extends CakeTestCase {

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
 * ファイル削除テスト
 *
 * @return void
 */
	public function testDeleteFileByKey() {
		$count2 = $this->CabinetFile->find('count', array('conditions' => array('key' => 1)));

		$this->assertEqual($count2, 2);

		$deleted = $this->CabinetFile->deleteFileByKey(1);
		$this->assertTrue($deleted);

		$count0 = $this->CabinetFile->find('count', array('conditions' => array('key' => 1)));
		$this->assertEqual($count0, 0);
	}

/**
 * カテゴリ無しで保存するテスト
 *
 * @return void
 */
	public function testSaveNoCategory() {
		$data = $this->CabinetFile->getNew();
		$data['CabinetFile']['category_id'] = 0;
		$data['CabinetFile']['title'] = 'title';
		$data['CabinetFile']['body1'] = 'body1';
		$data['CabinetFile']['key'] = '';
		$data['CabinetFile']['status'] = 2;
		$data['CabinetFile']['key'] = 0;
		$data['CabinetFile']['language_id'] = 1;
		$data['CabinetFile']['publish_start'] = '2015-01-01 00:00:00';
		$data['CabinetFile']['block_id'] = 5;
		$data['CabinetFile']['cabinet_key'] = 'cabinet1';

		$savedData = $this->CabinetFile->save($data);
		$this->assertTrue(isset($savedData['CabinetFile']['id']));
	}

/**
 * コンテンツ削除時にコメントも削除が実行されるテスト
 *
 * @return void
 */
	public function testCommentDelete() {
		// key=1 のテストデータのkeyはkey1なのでComment->deleteByContentKye('key1')がコールされるかテスト
		$mock = $this->getMockForModel('Comments.Comment', ['deleteByContentKey']);
		$mock->expects($this->once())
			->method('deleteByContentKey')
			->with(
				$this->equalTo('key1')
			);

		$this->CabinetFile->deleteFileByKey(1);
	}

/**
 * 削除失敗時に例外がなげられるテスト
 *
 * @return void
 */
	public function testDeleteFail() {
		$CabinetFileMock = $this->getMockForModel('Cabinets.CabinetFile', ['deleteAll']);
		$CabinetFileMock->expects($this->once())
			->method('deleteAll')
			->will($this->returnValue(false));

		// 例外のテスト
		$this->setExpectedException('InternalErrorException');
		$CabinetFileMock->Behaviors->unload('Tag');
		$CabinetFileMock->Behaviors->unload('Trackable');
		$CabinetFileMock->Behaviors->unload('Like');
		$CabinetFileMock->deleteFileByKey(1);
	}

/**
 * test saveFile
 *
 * @return void
 */
	public function testSaveFile() {
		$CommentMock = $this->getMockForModel('Comments.Comment', ['validateByStatus']);
		$CommentMock->expects($this->once())
			->method('validateByStatus')
			->will($this->returnValue(true));

		$data = $this->CabinetFile->getNew();
		$data['CabinetFile']['category_id'] = 0;
		$data['CabinetFile']['title'] = 'testSaveFile';
		$data['CabinetFile']['body1'] = 'body1';
		$data['CabinetFile']['key'] = '';
		$data['CabinetFile']['status'] = 3;
		$data['CabinetFile']['key'] = 0;
		$data['CabinetFile']['language_id'] = 1;
		$data['CabinetFile']['publish_start'] = '2015-01-01 00:00:00';
		$data['CabinetFile']['block_id'] = 5;
		$data['CabinetFile']['cabinet_key'] = 'cabinet1';

		$result = $this->CabinetFile->saveFile(6, 6, $data);
		$this->assertTrue(isset($result['CabinetFile']['id']));
	}

/**
 * test saveFile validate fail
 *
 * @return void
 */
	public function testSaveFileInvalid() {
		$data = $this->CabinetFile->getNew();
		$data['CabinetFile']['category_id'] = 0;
		$data['CabinetFile']['title'] = ''; // invalid
		$data['CabinetFile']['body1'] = 'body1';
		$data['CabinetFile']['key'] = '';
		$data['CabinetFile']['status'] = 3;
		$data['CabinetFile']['key'] = 0;
		$data['CabinetFile']['language_id'] = 1;
		$data['CabinetFile']['publish_start'] = '2015-01-01 00:00:00';
		$data['CabinetFile']['block_id'] = 5;

		$result = $this->CabinetFile->saveFile(6, 6, $data);
		$this->assertFalse($result);
	}

/**
 * test saveFile save fail
 *
 * @return void
 */
	public function testSaveFileFailed() {
		$CabinetFileMock = $this->getMockForModel('Cabinets.CabinetFile', ['save']);
		$CabinetFileMock->expects($this->once())
			->method('save')
			->will($this->returnValue(false));

		$data = $this->CabinetFile->getNew();
		$data['CabinetFile']['category_id'] = 0;
		$data['CabinetFile']['title'] = 'Title';
		$data['CabinetFile']['body1'] = 'body1';
		$data['CabinetFile']['key'] = '';
		$data['CabinetFile']['status'] = 3;
		$data['CabinetFile']['key'] = 0;
		$data['CabinetFile']['language_id'] = 1;
		$data['CabinetFile']['publish_start'] = '2015-01-01 00:00:00';
		$data['CabinetFile']['block_id'] = 5;

		// 例外のテスト
		$this->setExpectedException('InternalErrorException');
		$CabinetFileMock->saveFile(6, 6, $data);
	}

/**
 * test saveFile コメントバリデーション失敗test
 *
 * @return void
 */
	public function testSaveFileCommentInvalid() {
		$CommentMock = $this->getMockForModel('Comments.Comment', ['validateByStatus']);
		$CommentMock->expects($this->once())
			->method('validateByStatus')
			->will($this->returnValue(false));

		$data = $this->CabinetFile->getNew();
		$data['CabinetFile']['category_id'] = 0;
		$data['CabinetFile']['title'] = 'testSaveFile';
		$data['CabinetFile']['body1'] = 'body1';
		$data['CabinetFile']['key'] = '';
		$data['CabinetFile']['status'] = 3;
		$data['CabinetFile']['key'] = 0;
		$data['CabinetFile']['language_id'] = 1;
		$data['CabinetFile']['publish_start'] = '2015-01-01 00:00:00';
		$data['CabinetFile']['block_id'] = 5;
		$data['CabinetFile']['cabinet_key'] = 'cabinet1';

		$result = $this->CabinetFile->saveFile(6, 6, $data);
		$this->assertFalse($result);
	}

/**
 * test saveFile コメントsave失敗
 *
 * @return void
 */
	public function testSaveFileSaveCommentFailed() {
		$CommentMock = $this->getMockForModel('Comments.Comment', ['validateByStatus', 'save']);
		$CommentMock->expects($this->once())
			->method('validateByStatus')
			->will($this->returnValue(true));
		$CommentMock->expects($this->once())
			->method('save')
			->will($this->returnValue(false));
		$CommentMock->data = true; // saveFile でthis->Comment->dataの有無チェックがあるので。

		$data = $this->CabinetFile->getNew();
		$data['CabinetFile']['category_id'] = 0;
		$data['CabinetFile']['title'] = 'testSaveFile';
		$data['CabinetFile']['body1'] = 'body1';
		$data['CabinetFile']['key'] = '';
		$data['CabinetFile']['status'] = 3;
		$data['CabinetFile']['key'] = 0;
		$data['CabinetFile']['language_id'] = 1;
		$data['CabinetFile']['publish_start'] = '2015-01-01 00:00:00';
		$data['CabinetFile']['block_id'] = 5;
		$data['CabinetFile']['cabinet_key'] = 'cabinet1';

		// 例外のテスト
		$this->setExpectedException('InternalErrorException');
		$this->CabinetFile->saveFile(6, 6, $data);
	}
}
