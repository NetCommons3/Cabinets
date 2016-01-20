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
class Controller_CabinetBlocks_IndexTest extends CabinetsAppControllerTestBase {

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
 * testIndex
 *
 * @return void
 */
	public function testIndex() {
		RolesControllerTest::login($this);

		$this->testAction(
			'/cabinets/cabinet_blocks/index/1',
			array(
				'method' => 'get',
			)
		);
		$this->assertInternalType('array', $this->vars['cabinets']);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * testIndex. No cabinets
 *
 * @return void
 */
	public function testIndexNoCabinets() {
		RolesControllerTest::login($this);

		// cabinetレコードを削除しておく
		$Cabinet = ClassRegistry::init('Cabinets.Cabinet');
		$Cabinet->deleteAll(array(1 => 1), false, false);

		$view = $this->testAction(
			'/cabinets/cabinet_blocks/index/1',
			array(
				'method' => 'get',
				'return' => 'view'
			)
		);
		$this->assertTextContains(__d('net_commons', 'Not found.'), $view);

		AuthGeneralControllerTest::logout($this);
	}
}

