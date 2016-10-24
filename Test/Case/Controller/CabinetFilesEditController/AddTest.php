<?php
/**
 * CabinetFilesEditController::add()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerAddTest', 'Workflow.TestSuite');
App::uses('TemporaryFolder', 'Files.Utility');

/**
 * CabinetFilesEditController::add()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Controller\CabinetFilesEditController
 */
class CabinetFilesEditControllerAddTest extends WorkflowControllerAddTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet',
		'plugin.cabinets.cabinet_file',
		'plugin.cabinets.cabinet_file_tree',
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
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'cabinet_files_edit';

/**
 * アップロードテストに使うファイルの情報
 *
 * @var array
 */
	protected $_uploadFile = [
		'name' => 'logo.gif',
		'type' => 'image/gif',
		'tmp_name' => '',
		'error' => 0,
		'size' => 5873,
	];

/**
 * テスト用のテンポラリフォルダ作成
 *
 * @param string $name The name parameter on PHPUnit_Framework_TestCase::__construct()
 * @param array  $data The date parameter on PHPUnit_Framework_TestCase::__construct()
 * @param string $dataName The dataName parameter on PHPUnit_Framework_TestCase::__construct()
 * @return void
 */
	//public function __construct($name = null, array $data = array(), $dataName = '') {
	//	parent::__construct($name, $data, $dataName);
	//
	//	$tempUploadFolder = new TemporaryFolder();
	//	$this->_uploadFile['tmp_name'] = $tempUploadFolder->path . DS . 'tempfile';
	//}

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		// アップロードパスの変更
		$tmpFolder = new TemporaryFolder();
		$this->controller->UploadFile = ClassRegistry::init('Files.UploadFile', true);
		$this->controller->UploadFile->uploadBasePath = $tmpFolder->path . '/';

		// アップロードテストに使うファイルを準備
		copy(APP . 'Plugin/Cabinets/Test/Fixture/logo.gif', $this->_getTempUploadFilePath());
	}

/**
 * テストDataの取得
 *
 * @return array
 */
	private function __data() {
		$frameId = '6';
		$blockId = '2';
		$blockKey = 'block_1';

		$data = array(
			'save_' . WorkflowComponent::STATUS_IN_DRAFT => null,
			'Frame' => array(
				'id' => $frameId,
			),
			'Block' => array(
				'id' => $blockId,
				'key' => $blockKey,
				'language_id' => '2',
				'room_id' => '2',
				'plugin_key' => $this->plugin,
			),

			//:必要のデータセットをここに書く
			'CabinetFile' => array(
				'id' => null,
				'key' => null,
				'language_id' => '2',
				'status' => null,
				'file' => $this->_uploadFile,
			),
			'CabinetFileTree' => [
				'parent_id' => 11,
			],

			'WorkflowComment' => array(
				'comment' => 'WorkflowComment save test',
			),
		);
		$data['CabinetFile']['file']['tmp_name'] = $this->_getTempUploadFilePath();

		return $data;
	}

/**
 * addアクションのGETテスト(ログインなし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderAddGet() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		// * ログインなし
		$results[0] = array(
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id']
			),
			'assert' => null, 'exception' => 'ForbiddenException',
		);

		return $results;
	}

/**
 * addアクションのGETテスト(作成権限あり)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderAddGetByCreatable() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		$results[0] = array(
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
			),
			'assert' => array('method' => 'assertNotEmpty'),
		);

		// * フレームID指定なしテスト
		array_push($results, Hash::merge($results[0], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertNotEmpty'),
		)));

		return $results;
	}

/**
 * addアクションのPOSTテスト用DataProvider
 *
 * ### 戻り値
 *  - data: 登録データ
 *  - role: ロール
 *  - urlOptions: URLオプション
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderAddPost() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		// * ログインなし
		$results[0] = array(
			'data' => $data, 'role' => null,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id']
			),
			'exception' => 'ForbiddenException'
		);
		// * 作成権限あり
		$results[1] = array(
			'data' => $data, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id']
			),
		);
		// * フレームID指定なしテスト
		$results[2] = array(
			'data' => $data, 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
			'urlOptions' => array(
				'frame_id' => null,
				'block_id' => $data['Block']['id']),
		);

		return $results;
	}

/**
 * addアクションのValidationErrorテスト用DataProvider
 *
 * ### 戻り値
 *  - data: 登録データ
 *  - urlOptions: URLオプション
 *  - validationError: バリデーションエラー
 *
 * @return array
 */
	public function dataProviderAddValidationError() {
		$data = $this->__data();
		$data['CabinetFile']['file']['name'] = '';
		$data['CabinetFile']['file']['error'] = UPLOAD_ERR_NO_FILE;

		$result = array(
			'data' => $data,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id']
			),
			'validationError' => array(),
		);

		//テストデータ
		$results = array();
		// add のエラーはFlashメッセージなのでViewを取得してもわからない。
		array_push($results, Hash::merge($result, array(
			'validationError' => array(
				'field' => 'CabinetFile.file.name', // エラーにするフィールド指定
				'value' => '',
				'message' => __d('cabinets', 'Add File') // エラーになったらフォームが再表示される
			)
		)));

		return $results;
	}

/**
 * Viewのアサーション
 *
 * @param array $data テストデータ
 * @return void
 */
	private function __assertAddGet($data) {
		$this->assertInput(
			'input', 'data[Frame][id]', $data['Frame']['id'], $this->view
		);
		$this->assertInput(
			'input', 'data[Block][id]', $data['Block']['id'], $this->view
		);

		// 上記以外に必要なassert追加
	}

/**
 * view(ctp)ファイルのテスト(公開権限なし)
 *
 * @return void
 */
	public function testViewFileByCreatable() {
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		//テスト実行
		$data = $this->__data();
		$this->_testGetAction(
			array(
				'action' => 'add',
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
			),
			array('method' => 'assertNotEmpty')
		);

		//チェック
		$this->__assertAddGet($data);
		$this->assertInput('button', 'save_' . WorkflowComponent::STATUS_IN_DRAFT, null, $this->view);
		$this->assertInput('button', 'save_' . WorkflowComponent::STATUS_APPROVED, null, $this->view);

		// 上記以外に必要なassert追加
		//debug($this->view);

		TestAuthGeneral::logout($this);
	}

/**
 * view(ctp)ファイルのテスト(公開権限あり)
 *
 * @return void
 */
	public function testViewFileByPublishable() {
		//ログイン
		TestAuthGeneral::login($this);

		//テスト実行
		$data = $this->__data();
		$this->_testGetAction(
			array(
				'action' => 'add',
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
			),
			array('method' => 'assertNotEmpty')
		);

		//チェック
		$this->__assertAddGet($data);
		$this->assertInput('button', 'save_' . WorkflowComponent::STATUS_IN_DRAFT, null, $this->view);
		$this->assertInput('button', 'save_' . WorkflowComponent::STATUS_PUBLISHED, null, $this->view);

		//上記以外に必要なassert追加
		//debug($this->view);

		//ログアウト
		TestAuthGeneral::logout($this);
	}

/**
 * テスト用にアップロードされたことにするテンポラリファイルのパスを返す
 *
 * @return string
 */
	protected function _getTempUploadFilePath() {
		static $path = null;
		if ($path === null) {
			$tempFolder = new TemporaryFolder();
			$path = $tempFolder->path . DS . 'tempfile';
		}
		return $path;
	}

}
