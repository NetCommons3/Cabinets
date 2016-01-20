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
 * CabinetsAppController Test Case
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Controller
 */
class CabinetsAppControllerTest extends CabinetsAppControllerTestBase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Configure::write('Config.language', 'ja');
		$this->generate(
			'Cabinets.CabinetsApp',
			[
				'methods' => [
					'throwBadRequest',
				],
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
 * test initTabs
 *
 * @return void
 */
	public function testInitTabs() {
		$mainActiveTab = 'block_index';
		$blockActiveTab = 'block_settings';
		$this->controller->viewVars['frameId'] = 1;
		$this->controller->viewVars['blockId'] = 5;
		$this->controller->initTabs($mainActiveTab, $blockActiveTab);

		$this->assertInternalType('array', $this->controller->viewVars['settingTabs']);
		$this->assertInternalType('array', $this->controller->viewVars['blockSettingTabs']);
	}

/**
 * test index
 *
 * @return void
 */
	public function testInitCabinetSuccess() {
		RolesControllerTest::login($this);

		$this->controller->viewVars['blockId'] = 5;
		$this->controller->viewVars['frameId'] = 1;
		$this->controller->viewVars['roomId'] = 1;
		$resultTrue = $this->controller->initCabinet();

		$this->assertTrue($resultTrue);

		//$this->testAction(
		//	'/cabinets/cabinet_files/index/1',
		//	array(
		//		'method' => 'get',
		//	)
		//);
		//$this->assertInternalType('array', $this->vars['cabinet']);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test initCabinet faild
 *
 * @return void
 */
	public function testInitCabinetGetCabinetFail() {
		RolesControllerTest::login($this);

		$CabinetMock = $this->getMockForModel('Cabinets.Cabinet', ['getCabinet']);
		$CabinetMock->expects($this->once())
			->method('getCabinet')
			->will($this->returnValue(false));

		$this->controller->expects($this->once())
			->method('throwBadRequest');

		$this->controller->viewVars['blockId'] = 5;
		$this->controller->viewVars['frameId'] = 1;
		$this->controller->viewVars['roomId'] = 1;
		$resultFalse = $this->controller->initCabinet();

		$this->assertFalse($resultFalse);
		AuthGeneralControllerTest::logout($this);
	}

/**
 * test init cabinet. get cabinetSetting faild
 *
 * @return void
 */
	public function testInitCabinetGetCabinetSettingFail() {
		RolesControllerTest::login($this);

		$CabinetSettingMock = $this->getMockForModel('Cabinets.CabinetSetting', ['getCabinetSetting']);
		$CabinetSettingMock->expects($this->once())
			->method('getCabinetSetting')
			->will($this->returnValue(false));

		$this->controller->viewVars['blockId'] = 5;
		$this->controller->viewVars['frameId'] = 1;
		$this->controller->viewVars['roomId'] = 1;
		$resultTrue = $this->controller->initCabinet();
		$this->assertTrue($resultTrue);

		$this->assertNull($this->controller->viewVars['cabinetSetting']['id']);
		AuthGeneralControllerTest::logout($this);
	}

	//public function testInitCabinetWithFrameSetting() {
	//	RolesControllerTest::login($this);
	//
	//	$this->controller->viewVars['blockId'] = 5;
	//	$this->controller->viewVars['frameId'] = 1;
	//	$this->controller->viewVars['roomId'] = 1;
	//	$this->controller->viewVars['frameKey'] = 'frame_1';
	//	$resultTrue = $this->controller->initCabinet(['cabinetFrameSetting']);
	//	$this->assertTrue($resultTrue);
	//
	//	$this->assertNull($this->controller->viewVars['cabinetSetting']['id']);
	//	AuthGeneralControllerTest::logout($this);
	//}

}

