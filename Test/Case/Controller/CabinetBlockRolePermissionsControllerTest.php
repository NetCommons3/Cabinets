<?php
/**
 * Created by PhpStorm.
 * User: ryuji
 * Date: 15/05/18
 * Time: 9:56
 */

App::uses('CabinetBlockRolePermissionsController', 'Cabinets.Controller');
App::uses('CabinetsAppControllerTestBase', 'Cabinets.Test/Case/Controller');

/**
 * CabinetsController Test Case
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Controller
 */
class CabinetBlockRolePermissionsControllerTest extends CabinetsAppControllerTestBase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		//$this->loadFixtures(
		//	'Rooms.RoomRole'
		//);
		parent::setUp();
		Configure::write('Config.language', 'ja');
		$this->_controllerMock = $this->generate(
			'Cabinets.CabinetBlockRolePermissions',
			[
				'methods' => [
					'throwBadRequest',
				],
				'components' => [
					'Auth' => ['user'],
					'Session',
					'Security',
					//'NetCommonsBlock' => ['validateBlockId'],
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
 * test edit
 *
 * @return void
 */
	public function testEdit() {
		RolesControllerTest::login($this);

		//$this->controller->NetCommonsBlock->expects($this->once())
		//	->method('validateBlockId')
		//	->will($this->returnValue(true));

		$this->testAction(
			'/cabinets/cabinet_block_role_permissions/edit/1/5',
			array(
				'method' => 'get',
			)
		);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test edit invalid block id
 *
 * @return void
 */
	public function testEditInvalidBlockId() {
		RolesControllerTest::login($this);

		//$this->controller->NetCommonsBlock->expects($this->once())
		//	->method('validateBlockId')
		//	->will($this->returnValue(false));

		$this->controller->expects($this->once())
			->method('throwBadRequest');

		$resultFalse = $this->testAction(
			'/cabinets/cabinet_block_role_permissions/edit/1/999',
			array(
				'method' => 'get',
			)
		);
		$this->assertFalse($resultFalse);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test edit. block not found
 *
 * @return void
 */
	public function testEditBlockNotFound() {
		RolesControllerTest::login($this);

		$BlockMock = $this->getMockForModel('Blocks.Block', ['find']);
		$BlockMock->expects($this->once())
			->method('find')
			->will($this->returnValue(false));

		//$this->controller->NetCommonsBlock->expects($this->once())
		//	->method('validateBlockId')
		//	->will($this->returnValue(true));

		$this->controller->expects($this->any())
			->method('throwBadRequest');

		$resultFalse = $this->testAction(
			'/cabinets/cabinet_block_role_permissions/edit/1/5',
			array(
				'method' => 'get',
			)
		);
		$this->assertFalse($resultFalse);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test edit. Post
 *
 * @return void
 */
	public function testEditPostSuccess() {
		RolesControllerTest::login($this);

		//$this->controller->NetCommonsBlock->expects($this->once())
		//	->method('validateBlockId')
		//	->will($this->returnValue(true));

		$data = [];
		$data['CabinetSetting']['cabinet_key'] = 'new_cabinet_key';
		$data['BlockRolePermission'] = array();

		$this->testAction(
			'/cabinets/cabinet_block_role_permissions/edit/1/5',
			array(
				'method' => 'post',
				'data' => $data,
			)
		);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test edit. Post validateFail
 *
 * @return void
 */
	public function testEditPostValidateFail() {
		RolesControllerTest::login($this);
		//
		//$this->controller->NetCommonsBlock->expects($this->once())
		//	->method('validateBlockId')
		//	->will($this->returnValue(true));

		$data = [];
		//$data['CabinetSetting']['cabinet_key'] = 'new_cabinet_key';
		$data['BlockRolePermission'] = array();

		$this->testAction(
			'/cabinets/cabinet_block_role_permissions/edit/1/5',
			array(
				'method' => 'post',
				'data' => $data,
			)
		);

		AuthGeneralControllerTest::logout($this);
	}
}

