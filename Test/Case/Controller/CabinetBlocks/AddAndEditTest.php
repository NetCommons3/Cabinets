<?php
/**
 * Created by PhpStorm.
 * User: ryuji
 * Date: 15/05/18
 * Time: 9:56
 */

App::uses('CabinetBlocksController', 'Cabinets.Controller');
App::uses('CabinetsAppControllerTestBase', 'Cabinets.Test/Case/Controller');

/**
 * CabinetsController Test Case
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Controller
 */
class Controller_CabinetBlocks_AddAndEditTest extends CabinetsAppControllerTestBase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Configure::write('Config.language', 'ja');
		$this->generate(
			'Cabinets.CabinetBlocks',
			[
				'components' => [
					'Auth' => ['user'],
					'Session',
					'Security',
					'NetCommonsBlock' => ['validateBlockId'],
				],
				'methods' => [
					'throwBadRequest',
				],
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
 * test add action.
 *
 * @return void
 */
	public function testAdd() {
		RolesControllerTest::login($this);

		$view = $this->testAction(
			'/cabinets/cabinet_blocks/add/1',
			array(
				'method' => 'get',
				'return' => 'view'
			)
		);

		$this->assertRegExp('/<form/', $view);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test add action. Post
 *
 * @return void
 */
	public function testAddPostValidateFail() {
		RolesControllerTest::login($this);

		$data = array();
		$data = [
			'Cabinet' => [
				'key' => '',
				'name' => '',
			]
		];
		$view = $this->testAction(
			'/cabinets/cabinet_blocks/add/1',
			array(
				'method' => 'post',
				'return' => 'view',
				'data' => $data,
			)
		);

		$this->assertTextContains(sprintf(__d('net_commons', 'Please input %s.'), __d('cabinets', 'Cabinet Name')), $view);
		//debug($view);
		//$this->assertRegExp('#/cabinets/cabinet_blocks/index/#', $this->headers['Location']);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test add action. Post success
 *
 * @return void
 */
	public function testAddPostSuccess() {
		RolesControllerTest::login($this);

		$data = [
			'Cabinet' => [
				'key' => '',
				'name' => 'cabinet name',
				'block_id' => 5,
			],
			'Frame' => [
				'id' => 1
			]
		];
		$this->testAction(
			'/cabinets/cabinet_blocks/add/1',
			array(
				'method' => 'post',
				'return' => 'view',
				'data' => $data,
			)
		);

		$this->assertRegExp('#/cabinets/cabinet_blocks/index/#', $this->headers['Location']);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test edit action.
 *
 * @return void
 */
	public function testEdit() {
		RolesControllerTest::login($this);

		$this->controller->NetCommonsBlock->expects($this->once())
			->method('validateBlockId')
			->will($this->returnValue(true));

		$view = $this->testAction(
			'/cabinets/cabinet_blocks/edit/1/5',
			array(
				'method' => 'get',
				'return' => 'view'
			)
		);

		$this->assertRegExp('/<form/', $view);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test edit. validateBlockId failed
 *
 * @return void
 */
	public function testEditFail() {
		RolesControllerTest::login($this);

		$this->controller->NetCommonsBlock->expects($this->once())
			->method('validateBlockId')
			->will($this->returnValue(false));

		$this->controller->expects($this->once())
			->method('throwBadRequest');

		$this->testAction(
			'/cabinets/cabinet_blocks/edit/1/5',
			array(
				'method' => 'get',
				'return' => 'view'
			)
		);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test edit action. Post
 *
 * @return void
 */
	public function testEditPostValidateFail() {
		RolesControllerTest::login($this);

		$this->controller->NetCommonsBlock->expects($this->once())
			->method('validateBlockId')
			->will($this->returnValue(true));

		$data = array();
		$data = [
			'Cabinet' => [
				'key' => '',
				'name' => '',
			]
		];
		$view = $this->testAction(
			'/cabinets/cabinet_blocks/edit/1/5',
			array(
				'method' => 'post',
				'return' => 'view',
				'data' => $data,
			)
		);

		$this->assertTextContains(sprintf(__d('net_commons', 'Please input %s.'), __d('cabinets', 'Cabinet Name')), $view);
		//debug($view);
		//$this->assertRegExp('#/cabinets/cabinet_blocks/index/#', $this->headers['Location']);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test edit action. Post success
 *
 * @return void
 */
	public function testEditPostSuccess() {
		RolesControllerTest::login($this);

		$this->controller->NetCommonsBlock->expects($this->once())
			->method('validateBlockId')
			->will($this->returnValue(true));

		$data = [
			'Cabinet' => [
				'key' => '',
				'name' => 'cabinet name',
				'block_id' => 5,
			],
			'Frame' => [
				'id' => 1
			]
		];
		$this->testAction(
			'/cabinets/cabinet_blocks/edit/1/5',
			array(
				'method' => 'post',
				'return' => 'view',
				'data' => $data,
			)
		);

		$this->assertRegExp('#/cabinets/cabinet_blocks/index/#', $this->headers['Location']);

		AuthGeneralControllerTest::logout($this);
	}
}

