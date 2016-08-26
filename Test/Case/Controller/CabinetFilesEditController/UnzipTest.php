<?php
/**
 * CabinetFilesEditController::unzip()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('WorkflowComponent', 'Workflow.Controller/Component');

/**
 * CabinetFilesEditController::unzip()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Controller\CabinetFilesEditController
 */
class CabinetFilesEditControllerUnzipTest extends NetCommonsControllerTestCase {

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
 * @param string $role ロール
 * @return array
 */
	private function __data($role = null) {
		$frameId = '6';
		$blockId = '2';
		$blockKey = 'block_1';
		if ($role === Role::ROOM_ROLE_KEY_GENERAL_USER) {
			$contentId = '3';
			$contentKey = 'content_key_2';
		} else {
			$contentId = '2';
			$contentKey = 'content_key_1';
		}

		$data = array(
			'save_' . WorkflowComponent::STATUS_IN_DRAFT => null,
			'Frame' => array(
				'id' => $frameId,
			),
			'Block' => array(
				'id' => $blockId,
				'key' => $blockKey,
				'language_id' => '2',
				'room_id' => '1',
				'plugin_key' => $this->plugin,
			),

			//必要のデータセットをここに書く
			//'' => array(
			//	'id' => $contentId,
			//	'key' => $contentKey,
			//	'language_id' => '2',
			//	'status' => null,
			//),

			'WorkflowComment' => array(
				'comment' => 'WorkflowComment save test',
			),
		);

		return $data;
	}

/**
 * test get
 *
 * @return void
 */
	public function testGet() {
		TestAuthGeneral::login($this);

		//テスト実行
		$data = $this->__data();
		$this->_testGetAction(
			array(
				'action' => 'edit',
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => 'content_key_1',
				'action' => 'unzip'

			),
			null,
			'MethodNotAllowedException',
			'vars'
		);

		//チェック

		TestAuthGeneral::logout($this);
	}

/**
 * test post
 *
 * @param string $role Role
 * @param string $exception 例外
 * @param string $message setFlashNotificationにセットされるメッセージ
 * @dataProvider dataProviderPost
 */
	public function testPost($role, $exception, $message = null) {
		TestAuthGeneral::login($this, $role);

		$this->generateNc(
			'Cabinets.CabinetFilesEdit',
			[
				'components' => [
					'NetCommons' => [
						'setFlashNotification'
					]
				]
			]
		);

		if ($message) {

			$this->controller->NetCommons->expects($this->once())
				->method('setFlashNotification')
				->with($this->equalTo($message));
		}

		//テスト実行
		$data = $this->__data();
		$data['action'] = 'unzip';
		$this->_testPostAction(
			'put',
			$data,
			array(
				'frame_id' => $data['Frame']['id'],
				'block_id' => $data['Block']['id'],
				'key' => 'content_key_1',
				'action' => 'unzip',
				//'parent_id' => 17,
			),
			$exception
		);

		//チェック

		TestAuthGeneral::logout($this);
	}

/**
 * unzip postのテスト用データ
 *
 * @return array テストデータ
 */
	public function dataProviderPost() {
		//$data = $this->__data();
		$results = array();
		$results[0] = [
			Role::ROOM_ROLE_KEY_GENERAL_USER,
			'ForbiddenException'
		];
		$results[1] = [
			Role::ROOM_ROLE_KEY_EDITOR,
			'ForbiddenException'
		];
		// ε(　　　　 v ﾟωﾟ)　＜ ファイルとFixuture用意してから
		//$results[2] = [
		//	Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
		//	null,
		//	__d('cabinets', 'Unzip success.')
		//];

		return $results;
	}

}
