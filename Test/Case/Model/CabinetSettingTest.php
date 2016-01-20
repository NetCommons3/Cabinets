<?php
/**
 * CabinetSetting Test Case
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('CabinetSetting', 'Cabinets.Model');

/**
 * Summary for CabinetSetting Test Case
 *
 * @property CabinetSetting $CabinetSetting
 */
class CabinetSettingTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet_setting',
		'plugin.blocks.block_role_permission',
		'plugin.users.user',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->CabinetSetting = ClassRegistry::init('Cabinets.CabinetSetting');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CabinetSetting);

		parent::tearDown();
	}

/**
 * testGetCabinetSetting method
 *
 * @return void
 */
	public function testGetCabinetSetting() {
		$cabinetKey = 'cabinet1';
		$cabinetSetting = $this->CabinetSetting->getCabinetSetting($cabinetKey);
		$this->assertEquals(1, $cabinetSetting['CabinetSetting']['id']);
	}

/**
 * testSaveCabinetSetting method
 *
 * @return void
 */
	public function testSaveCabinetSetting() {
		$data = $this->CabinetSetting->getNew();
		$data['CabinetSetting']['cabinet_key'] = 'new_cabinet_key';
		$data['BlockRolePermission'] = array();
		$resultTrue = $this->CabinetSetting->saveCabinetSetting($data);
		$this->assertTrue($resultTrue);

		// validate fail
		$CabinetSettingMock = $this->getMockForModel('Cabinets.CabinetSetting', ['validateCabinetSetting']);
		$CabinetSettingMock->expects($this->once())
			->method('validateCabinetSetting')
			->will($this->returnValue(false));
		$data = $this->CabinetSetting->getNew();
		$data['CabinetSetting']['cabinet_key'] = 'new_cabinet_key';
		$data['BlockRolePermission'] = array();

		$resultFalse = $CabinetSettingMock->saveCabinetSetting($data);
		$this->assertFalse($resultFalse);

		// save fail
		$CabinetSettingMock = $this->getMockForModel('Cabinets.CabinetSetting', ['save']);
		$CabinetSettingMock->expects($this->once())
			->method('save')
			->will($this->returnValue(false));
		$data = $this->CabinetSetting->getNew();
		$data['CabinetSetting']['cabinet_key'] = 'new_cabinet_key';
		$data['BlockRolePermission'] = array();

		$this->setExpectedException('InternalErrorException');
		$CabinetSettingMock->saveCabinetSetting($data);
	}

/**
 * test saveCabinetSetting BlockRolePermission保存失敗系テスト
 *
 * @return void
 */
	public function testSaveCabinetSettingWithBlockRolePermissionFail() {
		// blockRolePermission validate fail
		$Mock = $this->getMockForModel('Blocks.BlockRolePermission', ['validateBlockRolePermissions']);
		$Mock->expects($this->once())
			->method('validateBlockRolePermissions')
			->will($this->returnValue(false));

		$data = $this->CabinetSetting->getNew();
		$data['CabinetSetting']['cabinet_key'] = 'new_cabinet_key';
		$data['BlockRolePermission'] = array('dummy');
		$resultFalse = $this->CabinetSetting->saveCabinetSetting($data);
		$this->assertFalse($resultFalse);

		$Mock = $this->getMockForModel('Blocks.BlockRolePermission', ['validateBlockRolePermissions', 'saveMany']);
		$Mock->expects($this->once())
			->method('validateBlockRolePermissions')
			->will($this->returnValue(true));
		$Mock->expects($this->once())
			->method('saveMany')
			->will($this->returnValue(false));

		$data = $this->CabinetSetting->getNew();
		$data['CabinetSetting']['cabinet_key'] = 'new_cabinet_key';
		$data['BlockRolePermission'] = array('dummy');

		$this->setExpectedException('InternalErrorException');
		$this->CabinetSetting->saveCabinetSetting($data);
	}

/**
 * testValidateCabinetSetting method
 *
 * @return void
 */
	public function testValidateCabinetSetting() {
		$data = $this->CabinetSetting->getNew();
		$data['CabinetSetting']['cabinet_key'] = 'new_cabinet_key';
		$resultTrue = $this->CabinetSetting->validateCabinetSetting($data);
		$this->assertTrue($resultTrue);
	}
}
