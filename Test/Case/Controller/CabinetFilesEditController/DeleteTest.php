<?php
/**
 * CabinetFilesEditController::delete()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerDeleteTest', 'Workflow.TestSuite');

/**
 * CabinetFilesEditController::delete()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Controller\CabinetFilesEditController
 */
class CabinetFilesEditControllerDeleteTest extends WorkflowControllerDeleteTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet',
		'plugin.cabinets.cabinet_file',
		'plugin.cabinets.cabinet_file_tree',
		'plugin.cabinets.cabinet_setting',
		'plugin.workflow.workflow_comment',
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
 * テストDataの取得
 *
 * @param string $contentKey キー
 * @return array
 */
	private function __data($contentKey = null) {
		$frameId = '6';
		$blockId = '2';
		$blockKey = 'block_1';
		if ($contentKey === 'content_key_2') {
			$contentId = '3';
		} elseif ($contentKey === 'content_key_4') {
			$contentId = '5';
		} else {
			$contentId = '2';
		}

		$data = array(
			'delete' => null,
			'Frame' => array(
				'id' => $frameId,
			),
			'Block' => array(
				'id' => $blockId,
				'key' => $blockKey,
			),

			//TODO:必要のデータセットをここに書く
			'' => array(
				'id' => $contentId,
				'key' => $contentKey,
			),
		);

		return $data;
	}

/**
 * deleteアクションのGETテスト用DataProvider
 *
 * ### 戻り値
 *  - role: ロール
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderDeleteGet() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		// * ログインなし
		$results[0] = array('role' => null,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => 'content_key_1',
			),
			'assert' => null, 'exception' => 'ForbiddenException'
		);
		// * 作成権限のみ(自分自身)
		array_push($results, Hash::merge($results[0], array(
			'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => 'content_key_2',
			),
			'assert' => null, 'exception' => 'BadRequestException'
		)));
		// * 編集権限、公開権限なし
		array_push($results, Hash::merge($results[0], array(
			'role' => Role::ROOM_ROLE_KEY_EDITOR,
			'assert' => null, 'exception' => 'BadRequestException'
		)));
		// * 公開権限あり
		array_push($results, Hash::merge($results[0], array(
			'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
			'assert' => null, 'exception' => 'BadRequestException'
		)));

		return $results;
	}

/**
 * deleteアクションのPOSTテスト用DataProvider
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
	public function dataProviderDeletePost() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		// * ログインなし
		$contentKey = 'content_key_1';
		array_push($results, array(
			'data' => $this->__data($contentKey),
			'role' => null,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => $contentKey
			),
			'exception' => 'ForbiddenException'
		));
		// * 作成権限のみ
		// ** 他人の記事
		$contentKey = 'content_key_1';
		array_push($results, array(
			'data' => $this->__data($contentKey),
			'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => $contentKey
			),
			'exception' => 'BadRequestException'
		));
		// ** 自分の記事＆一度も公開されていない
		$contentKey = 'content_key_2';
		array_push($results, array(
			'data' => $this->__data($contentKey),
			'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => $contentKey
			),
		));
		// ** 自分の記事＆一度公開している
		$contentKey = 'content_key_4';
		array_push($results, array(
			'data' => $this->__data($contentKey),
			'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => $contentKey
			),
			'exception' => 'BadRequestException'
		));
		// * 編集権限あり
		// ** 公開していない
		$contentKey = 'content_key_2';
		array_push($results, array(
			'data' => $this->__data($contentKey),
			'role' => Role::ROOM_ROLE_KEY_EDITOR,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => $contentKey
			),
		));
		// ** 公開している
		$contentKey = 'content_key_4';
		array_push($results, array(
			'data' => $this->__data($contentKey),
			'role' => Role::ROOM_ROLE_KEY_EDITOR,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => $contentKey
			),
			'exception' => 'BadRequestException'
		));
		// * 公開権限あり
		// ** フレームID指定なしテスト
		$contentKey = 'content_key_1';
		array_push($results, array(
			'data' => $this->__data($contentKey),
			'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
			'urlOptions' => array(
				'frame_id' => null,
				'block_id' => $data['Block']['id'],
				'key' => $contentKey
			),
		));

		return $results;
	}

/**
 * deleteアクションのExceptionErrorテスト用DataProvider
 *
 * ### 戻り値
 *  - mockModel: Mockのモデル
 *  - mockMethod: Mockのメソッド
 *  - data: 登録データ
 *  - urlOptions: URLオプション
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderDeleteExceptionError() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		$results[0] = array(
			'mockModel' => 'Cabinets.Xxxxxx', //TODO:Mockモデルをセットする
			'mockMethod' => 'deleteXxxxxx', //TODO:Mockメソッドをセットする
			'data' => $data,
			'urlOptions' => array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => 'content_key_1',
			),
			'exception' => 'BadRequestException',
			'return' => 'view'
		);

		//TODO:必要なデータをここに書く

		return $results;
	}

}
