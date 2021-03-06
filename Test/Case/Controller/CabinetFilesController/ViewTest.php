<?php
/**
 * CabinetFilesController::view()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerViewTest', 'Workflow.TestSuite');

/**
 * CabinetFilesController::view()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Controller\CabinetFilesController
 */
class CabinetFilesControllerViewTest extends WorkflowControllerViewTest {

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
		$contentKey = 'content_key_1';

		$data = array(
			'action' => 'view',
			'frame_id' => $frameId,
			'block_id' => $blockId,
			'key' => $contentKey,
		);

		return $data;
	}

/**
 * viewアクションのテスト用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderView() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		$results[0] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_1'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[1] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_2'),
			'assert' => null, 'exception' => 'BadRequestException'
		);
		$results[2] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_3'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[3] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_4'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[4] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_5'),
			'assert' => null, 'exception' => 'BadRequestException'
		);
		$results[5] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_999'),
			'assert' => null, 'exception' => 'BadRequestException'
		);
		$results[6] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_10'),
			'assert' => null, 'exception' => 'BadRequestException'
		);

		return $results;
	}

/**
 * viewアクションのテスト
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderView
 * @return void
 */
	public function testView($urlOptions, $assert, $exception = null, $return = 'view') {
		//テスト実行
		parent::testView($urlOptions, $assert, $exception, $return);
		if ($exception) {
			return;
		}

		//チェック
		$this->__assertView($urlOptions['key'], false);
	}

/**
 * viewアクションのテスト(作成権限のみ)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderViewByCreatable() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		$results[0] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_1'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[1] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_2'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[2] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_3'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[3] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_4'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[4] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_5'),
			'assert' => null, 'exception' => 'BadRequestException'
		);
		$results[5] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_999'),
			'assert' => null, 'exception' => 'BadRequestException'
		);

		return $results;
	}

/**
 * viewアクションのテスト(作成権限のみ)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderViewByCreatable
 * @return void
 */
	public function testViewByCreatable($urlOptions, $assert, $exception = null, $return = 'view') {
		//テスト実行
		parent::testViewByCreatable($urlOptions, $assert, $exception, $return);
		if ($exception) {
			return;
		}

		//チェック
		if ($urlOptions['key'] === 'content_key_1') {
			$this->__assertView($urlOptions['key'], false);

		} elseif ($urlOptions['key'] === 'content_key_3') {
			$this->__assertView($urlOptions['key'], true);

		} elseif ($urlOptions['key'] === 'content_key_4') {
			$this->__assertView($urlOptions['key'], false);

		} else {
			$this->__assertView($urlOptions['key'], false);
		}
	}

/**
 * viewアクションのテスト用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderViewByEditable() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		$results[0] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_1'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[1] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_2'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[2] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_3'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[3] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_4'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[4] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_5'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[5] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_999'),
			'assert' => null, 'exception' => 'BadRequestException'
		);

		return $results;
	}

/**
 * viewアクションのテスト(編集権限あり)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderViewByEditable
 * @return void
 */
	public function testViewByEditable($urlOptions, $assert, $exception = null, $return = 'view') {
		//テスト実行
		parent::testViewByEditable($urlOptions, $assert, $exception, $return);
		if ($exception) {
			return;
		}

		//チェック
		$this->__assertView($urlOptions['key'], true);
	}

/**
 * view()のassert
 *
 * @param string $contentKey コンテンツキー
 * @param bool $isLatest 最終コンテンツかどうか
 * @return void
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	private function __assertView($contentKey, $isLatest = false) {
		//view(ctp)ファイルに対するassert追加
		//debug($this->view);

		if ($contentKey === 'content_key_1') {
			if ($isLatest) {
				// コンテンツのデータ(id=2, key=content_key_1)に対する期待値
				$this->assertTextContains('file2', $this->view);
			} else {
				// コンテンツのデータ(id=1, key=content_key_1)に対する期待値
				$this->assertTextContains('file1', $this->view);
			}

		} elseif ($contentKey === 'content_key_2') {
			// コンテンツのデータ(id=3, key=content_key_2)に対する期待値
			$this->assertTextContains('file3', $this->view);

		} elseif ($contentKey === 'content_key_3') {
			if ($isLatest) {
				// コンテンツのデータ(id=5, key=content_key_3)に対する期待値
				$this->assertTextContains('file5', $this->view);
			} else {
				// コンテンツのデータ(id=4, key=content_key_3)に対する期待値
				$this->assertTextContains('file4', $this->view);
			}

		} elseif ($contentKey === 'content_key_4') {
			if ($isLatest) {
				// コンテンツのデータ(id=7, key=content_key_4)に対する期待値
				$this->assertTextContains('file7', $this->view);
			} else {
				// コンテンツのデータ(id=6, key=content_key_4)に対する期待値
				$this->assertTextContains('file6', $this->view);
			}

		} elseif ($contentKey === 'content_key_5') {
			// コンテンツのデータ(id=8, key=content_key_5)に対する期待値
			$this->assertTextContains('file8', $this->view);
		}
	}

}
