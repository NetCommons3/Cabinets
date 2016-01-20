<?php
/**
 * Created by PhpStorm.
 * User: ryuji
 * Date: 15/05/18
 * Time: 9:56
 */

App::uses('CabinetFilesEditController', 'Cabinets.Controller');
App::uses('CabinetsAppControllerTestBase', 'Cabinets.Test/Case/Controller');

/**
 * CabinetsController Test Case
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Controller
 */
class CabinetsFilesEdit_DeleteButtonTest extends CabinetsAppControllerTestBase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->cabinetFilesEditMock = $this->generate(
			'Cabinets.CabinetFilesEdit',
			[
				'methods' => [
					'handleValidationError',
				],
				'components' => [
					'Auth' => ['user'],
					'Session',
					'Security',
					'NetCommons.NetCommonsWorkflow'
				]
			]
		);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		Configure::write('Config.language', null);
		CakeSession::write('Auth.User', null);
		parent::tearDown();
	}

/**
 * コンテンツ新規登録フォームではゴミ箱アイコン非表示をテスト
 *
 * @return void
 */
	public function testAddFormNoDeleteButton() {
		RolesControllerTest::login($this);

		$view = $this->testAction(
			'/cabinets/cabinet_files_edit/add/1',
			array(
				'method' => 'get',
				'return' => 'view',
			)
		);
		$this->assertTextNotContains('glyphicon-trash', $view, debug($view, true));

		AuthGeneralControllerTest::logout($this);
	}

/**
 * コンテンツ編集フォームではゴミ箱アイコンが表示されるテスト
 *
 * @return void
 */
	public function testEditFormWithDeleteButton() {
		RolesControllerTest::login($this);

		$view = $this->testAction(
			'/cabinets/cabinet_files_edit/edit/1/key:3',
			array(
				'method' => 'get',
				'return' => 'view',
			)
		);
		$this->assertTextContains('glyphicon-trash', $view, debug($view, true));

		AuthGeneralControllerTest::logout($this);
	}

/**
 * 一度も公開されてないコンテンツは作成権限でも削除可能（削除ボタン表示）
 *
 * @return void
 */
	public function testYetPublishedIsViewDeleteButtonForEditor() {
		RolesControllerTest::login($this, Role::ROLE_KEY_EDITOR);

		$view = $this->testAction(
			'/cabinets/cabinet_files_edit/edit/1/key:4',
			array(
				'method' => 'get',
				'return' => 'view',
			)
		);
		$this->assertTextContains('glyphicon-trash', $view, debug($view, true));

		AuthGeneralControllerTest::logout($this);
	}

/**
 * 一度でも公開されたコンテンツの削除には公開権限が必用
 *
 * 公開権限ありで削除ボタン表示
 * 公開権限無しなら削除ボタン非表示
 *
 * @return void
 */
	public function testPublishedIsNotViewDeleteButtonForEditor() {
		// 公開権限なしなら公開済みコンテンツは削除NG
		RolesControllerTest::login($this, Role::ROLE_KEY_EDITOR);

		$view = $this->testAction(
			'/cabinets/cabinet_files_edit/edit/1/key:3',
			array(
				'method' => 'get',
				'return' => 'view',
			)
		);
		$this->assertTextNotContains('glyphicon-trash', $view, debug($view, true));

		AuthGeneralControllerTest::logout($this);

		// 公開権限あれば公開済みでも削除ボタンが表示される
		RolesControllerTest::login($this, Role::ROLE_KEY_SYSTEM_ADMINISTRATOR);

		$view = $this->testAction(
			'/cabinets/cabinet_files_edit/edit/1/key:3',
			array(
				'method' => 'get',
				'return' => 'view',
			)
		);
		$this->assertTextContains('glyphicon-trash', $view, debug($view, true));

		AuthGeneralControllerTest::logout($this);
	}

}

