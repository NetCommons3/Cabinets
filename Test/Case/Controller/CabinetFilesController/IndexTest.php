<?php
/**
 * CabinetFilesController::index()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerIndexTest', 'Workflow.TestSuite');

/**
 * CabinetFilesController::index()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Controller\CabinetFilesController
 */
class CabinetFilesControllerIndexTest extends WorkflowControllerIndexTest {

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
		'plugin.workflow.workflow_comment',
		'plugin.authorization_keys.authorization_keys',
		'plugin.site_manager.site_setting',
		'plugin.cabinets.upload_file_for_cabinets',
		'plugin.cabinets.upload_files_content_for_cabinets',
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
	protected $_controller = 'cabinet_files';

/**
 * テストDataの取得
 *
 * @return array
 */
	private function __data() {
		$frameId = '6';
		$blockId = '2';

		$data = array(
			'action' => 'index',
			'frame_id' => $frameId,
			'block_id' => $blockId,
		);

		return $data;
	}

/**
 * indexアクションのテスト(ログインなし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndex() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		$results[0] = array(
			'urlOptions' => $data,
			'assert' => array('method' => 'assertNotEmpty'),
		);

		return $results;
	}

/**
 * indexアクションのテスト
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderIndex
 * @return void
 */
	public function testIndex($urlOptions, $assert, $exception = null, $return = 'view') {
		//テスト実行
		parent::testIndex($urlOptions, $assert, $exception, $return);

		// 追加ボタンがない
		$this->assertNotContains('addFile()', $this->view);
		//チェック
	}

/**
 * indexアクションのテスト(作成権限あり)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndexByCreatable() {
		return array($this->dataProviderIndex()[0]);
	}

/**
 * indexアクションのテスト(作成権限のみ)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderIndexByCreatable
 * @return void
 */
	public function testIndexByCreatable($urlOptions, $assert, $exception = null, $return = 'view') {
		//テスト実行
		parent::testIndexByCreatable($urlOptions, $assert, $exception, $return);

		//チェック
		// 追加ボタンがある
		$this->assertContains('addFile()', $this->view);
		// 自分で作成したファイルは編集出来る
		$this->assertContains(
			'cabinet_files_edit/edit/' . $urlOptions['block_id'] . '/content_key_2',
			$this->view);
		// 他人が作成したファイルは編集出来ない
		$this->assertNotContains(
			'cabinet_files_edit/edit/' . $urlOptions['block_id'] . '/content_key_1',
			$this->view);
		// フォルダ作成ボタンがない
		$this->assertNotContains('cabinet_files_edit/add_folder', $this->view);
	}

/**
 * indexアクションのテスト(編集権限あり)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndexByEditable() {
		return array($this->dataProviderIndex()[0]);
	}

/**
 * indexアクションのテスト(編集権限あり)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderIndexByEditable
 * @return void
 */
	public function testIndexByEditable($urlOptions, $assert, $exception = null, $return = 'view') {
		//テスト実行
		parent::testIndexByEditable($urlOptions, $assert, $exception, $return);

		//チェック
		// 他人が作成したファイルも編集出来る
		$this->assertContains(
			'cabinet_files_edit/edit/' . $urlOptions['block_id'] . '/content_key_1',
			$this->view);
		// フォルダ作成ボタンがない
		$this->assertNotContains('cabinet_files_edit/add_folder', $this->view);
	}

/**
 * indexアクションのテスト(公開権限あり)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderIndexByEditable
 * @return void
 */
	public function testIndexByPublishable($urlOptions, $assert, $exception = null, $return =
	'view') {
		//ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR);

		//テスト実施
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'index',
		), $urlOptions);

		$this->_testGetAction($url, $assert, $exception, $return);

		// フォルダ作成ボタンがある
		$this->assertContains('cabinet_files_edit/add_folder', $this->view);

		//ログアウト
		TestAuthGeneral::logout($this);
	}

}
