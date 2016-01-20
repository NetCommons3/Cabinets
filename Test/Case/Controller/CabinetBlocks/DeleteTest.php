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
class Controller_CabinetBlocks_DeleteTest extends CabinetsAppControllerTestBase {

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
 * test delete. validateBlockId failed
 *
 * @return void
 */
	public function testDeleteFail() {
		RolesControllerTest::login($this);

		$this->controller->NetCommonsBlock->expects($this->once())
			->method('validateBlockId')
			->will($this->returnValue(false));

		$this->controller->expects($this->once())
			->method('throwBadRequest');

		$data = [
			'Cabinet' => [
				'key' => 'cabinet1',
			],
			'Block' => [
				'id' => 5,
				'key' => 'block_5',
			]
		];
		$this->testAction(
			'/cabinets/cabinet_blocks/delete/1/5',
			array(
				'method' => 'delete',
				'data' => $data,
			)
		);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test delete action. Post
 *
 * @return void
 */
	public function testDeleteNotDeleteMethod() {
		RolesControllerTest::login($this);

		$this->controller->NetCommonsBlock->expects($this->once())
			->method('validateBlockId')
			->will($this->returnValue(true));

		$this->controller->expects($this->once())
			->method('throwBadRequest');

		$data = [
			'Cabinet' => [
				'key' => 'cabinet1',
			],
			'Block' => [
				'id' => 5,
				'key' => 'block_5',
			]
		];
		$this->testAction(
			'/cabinets/cabinet_blocks/delete/1/5',
			array(
				'method' => 'get',
				'data' => $data,
			)
		);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test delete action. Post success
 *
 * @return void
 */
	public function testDeletePostSuccess() {
		RolesControllerTest::login($this);

		$this->controller->NetCommonsBlock->expects($this->once())
			->method('validateBlockId')
			->will($this->returnValue(true));

		$data = [
			'Cabinet' => [
				'key' => 'cabinet1',
			],
			'Block' => [
				'id' => 5,
				'key' => 'block_5',
			]
		];
		$this->testAction(
			'/cabinets/cabinet_blocks/delete/1/5',
			array(
				'method' => 'delete',
				'data' => $data,
			)
		);

		$this->assertRegExp('#/cabinets/cabinet_blocks/index/#', $this->headers['Location']);

		AuthGeneralControllerTest::logout($this);
	}
}

