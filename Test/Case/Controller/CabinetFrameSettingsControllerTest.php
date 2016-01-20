<?php
/**
 * CabinetFrameSettingsController Test Case
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

//App::uses('BlocksController', 'Cabinets.Controller');
App::uses('CabinetsAppControllerTestBase', 'Cabinets.Test/Case/Controller');

/**
 * CabinetsController Test Case
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Controller
 */
class CabinetFrameSettingsControllerTest extends CabinetsAppControllerTestBase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Configure::write('Config.language', 'ja');
		$this->generate(
			'Cabinets.CabinetFrameSettings',
			[
				'components' => [
					'Auth' => ['user'],
					'Session',
					'Security',
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
 * test edit.
 *
 * @return void
 */
	public function testEdit() {
		RolesControllerTest::login($this);

		$view = $this->testAction(
			'/cabinets/cabinet_frame_settings/edit/1',
			array(
				'method' => 'get',
				'return' => 'view'
			)
		);

		$this->assertRegExp('/<form/', $view);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test edit.not found cabinetFrameSetting
 *
 * @return void
 */
	public function testEditNotFoundCabinetFrameSetting() {
		RolesControllerTest::login($this);

		$view = $this->testAction(
			'/cabinets/cabinet_frame_settings/edit/202',
			array(
				'method' => 'get',
				'return' => 'view'
			)
		);

		$this->assertRegExp('/<form/', $view);
		$this->assertFalse(isset($this->vars['cabinetFrameSetting']['id']));

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test edit. post fail
 *
 * @return void
 */
	public function testEditPostValidateError() {
		RolesControllerTest::login($this);

		$data = array();

		$view = $this->testAction(
			'/cabinets/cabinet_frame_settings/edit/1',
			array(
				'method' => 'post',
				'data' => $data,
				'return' => 'view'
			)
		);

		$this->assertRegExp('/<form/', $view);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test edit. post sucess
 *
 * @return void
 */
	public function testEditPostSuccess() {
		RolesControllerTest::login($this);

		$data = [
			'CabinetFrameSetting' => [
				'frame_key' => 'frame_1',
				'posts_per_page' => 1,
			]
		];

		$this->testAction(
			'/cabinets/cabinet_frame_settings/edit/1',
			array(
				'method' => 'post',
				'data' => $data,
			)
		);

		$this->assertRegExp('#/cabinets/cabinet_blocks/index/#', $this->headers['Location']);

		AuthGeneralControllerTest::logout($this);
	}

}