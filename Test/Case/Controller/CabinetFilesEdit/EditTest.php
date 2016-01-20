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
class CabinetsFilesEdit_EditTest extends CabinetsAppControllerTestBase {

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
 * test edit action 編集対象コンテンツがなかったとき
 *
 * @return void
 */
	public function testEditNotFound() {
		$this->setExpectedException('NotFoundException');

		RolesControllerTest::login($this);
		$this->testAction(
			'/cabinets/cabinet_files_edit/edit/1/key:100',
			array(
				'method' => 'get',
			)
		);
		AuthGeneralControllerTest::logout($this);
	}

/**
 * test edit action. 権限がないとき
 *
 * @return void
 */
	public function testEditNoEditPermission() {
		RolesControllerTest::login($this, 'general_user');
		// key:1作成ユーザと異なるuser idを返させる
		$this->cabinetFilesEditMock->Auth->expects($this->any())
			->method('user')
			->will($this->returnValue(4));

		// 編集権限無しで他のユーザのコンテンツはedit NG
		$this->setExpectedException('ForbiddenException');
		$this->testAction(
			'/cabinets/cabinet_files_edit/edit/1/key:1',
			array(
				'method' => 'get',
			)
		);
		AuthGeneralControllerTest::logout($this);
	}

/**
 * test edit action. 権限がないとき
 *
 * @return void
 */
	public function testEditNoEditPermission4Visitor() {
		RolesControllerTest::login($this, Role::ROLE_KEY_VISITOR);
		// key:1作成ユーザと異なるuser idを返させる
		$this->cabinetFilesEditMock->Auth->expects($this->any())
			->method('user')
			->will($this->returnValue(4));

		// 編集権限無しで他のユーザのコンテンツはedit NG
		$this->setExpectedException('ForbiddenException');
		$this->testAction(
			'/cabinets/cabinet_files_edit/edit/1/key:1',
			array(
				'method' => 'get',
			)
		);
		AuthGeneralControllerTest::logout($this);
	}

/**
 * test edit action. validate fail
 *
 * @return void
 */
	public function testEditPutValidateFail() {
		$this->cabinetFilesEditMock->NetCommonsWorkflow->expects($this->once())
			->method('parseStatus')
			->will($this->returnValue(1));

		RolesControllerTest::login($this);

		// validate error発生でhandleValidationError()が呼ばれる。
		$this->cabinetFilesEditMock->expects($this->once())
			->method('handleValidationError')
			->with($this->isType('array'));
		$this->testAction(
			'/cabinets/cabinet_files_edit/edit/1/key:1',
			array(
				'method' => 'put',
			)
		);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test edit action. put success
 *
 * @return void
 */
	public function testEditPutSuccess() {
		$this->cabinetFilesEditMock->NetCommonsWorkflow->expects($this->once())
			->method('parseStatus')
			->will($this->returnValue(1));

		RolesControllerTest::login($this);

		$CabinetFile = ClassRegistry::init('Cabinets.CabinetFile');

		$data = $CabinetFile->findByKeyAndIsLatest(1, 1);

		$data['CabinetFile']['title'] = 'Edit title';
		$data['Comment']['comment'] = '';

		$this->testAction(
			'/cabinets/cabinet_files_edit/edit/1/key:1',
			array(
				'method' => 'put',
				'data' => $data,
			)
		);
		$this->assertRegExp('#cabinets/cabinet_files/view/1/key:1#', $this->headers['Location']);
		AuthGeneralControllerTest::logout($this);
	}

}

