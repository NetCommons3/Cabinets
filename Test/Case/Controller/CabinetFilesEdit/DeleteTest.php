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
class Controller_CabinetsFilesEdit_DeleteTest extends CabinetsAppControllerTestBase {

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
					'NetCommons.NetCommonsWorkflow',
					'Cabinets.CabinetFilePermission' => ['canDelete'],
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
 * testDelete
 *
 * @return void
 */
	public function testDelete() {
		RolesControllerTest::login($this);

		$this->testAction(
			'/cabinets/cabinet_files_edit/delete/1',
			array(
				'method' => 'post',
				'return' => 'view',
				'data' => array(
					'CabinetFile' => array('key' => 3)
				)
			)
		);
		$this->assertRegExp('#/cabinets/cabinet_files/index#', $this->headers['Location']);

		$CabinetFile = ClassRegistry::init('Cabinets.CabinetFile');
		$countZero = $CabinetFile->find('count', array('conditions' => array('key' => 3)));
		$this->assertEquals(0, $countZero);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test delete. No permission
 *
 * @return void
 */
	public function testDeleteNoPermission() {
		RolesControllerTest::login($this);

		$this->cabinetFilesEditMock->CabinetFilePermission->expects($this->any())
			->method('canDelete')
			->will($this->returnValue(false));

		$this->setExpectedException('ForbiddenException');

		$this->testAction(
			'/cabinets/cabinet_files_edit/delete/1',
			array(
				'method' => 'post',
				'return' => 'view',
				'data' => array(
					'CabinetFile' => array('key' => 3)
				)
			)
		);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test delete. DeleteFail
 *
 * @return void
 */
	public function testDeleteDeleteFail() {
		RolesControllerTest::login($this);

		$CabinetFileMock = $this->getMockForModel('Cabinets.CabinetFile', ['deleteFileByKey']);
		$CabinetFileMock->expects($this->any())
			->method('deleteFileByKey')
			->will($this->returnValue(false));
		$this->setExpectedException('InternalErrorException');

		$this->testAction(
			'/cabinets/cabinet_files_edit/delete/1',
			array(
				'method' => 'post',
				'return' => 'view',
				'data' => array(
					'CabinetFile' => array('key' => 3)
				)
			)
		);

		AuthGeneralControllerTest::logout($this);
	}
}

