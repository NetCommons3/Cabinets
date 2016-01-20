<?php
/**
 * Cabinet Test Case
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('Cabinet', 'Cabinets.Model');
App::uses('CabinetsAppModelTestBase', 'Cabinets.Test/Case/Model');

/**
 * Summary for Cabinet Test Case
 *
 * @property Cabinet $Cabinet
 */
class CabinetTest extends CabinetsAppModelTestBase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet',
		'plugin.cabinets.cabinet_file',
		'plugin.cabinets.cabinet_setting',
		'plugin.blocks.block',
		'plugin.blocks.block_role_permission',
		'plugin.rooms.room',
		'plugin.rooms.roles_room',
		'plugin.categories.category',
		'plugin.categories.categoryOrder',
		'plugin.frames.frame',
		'plugin.boxes.box',
		'plugin.cabinets.plugin',
		'plugin.m17n.language',
		'plugin.users.user',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Cabinet = ClassRegistry::init('Cabinets.Cabinet');
		$this->_unloadTrackable($this->Cabinet);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Cabinet);

		parent::tearDown();
	}

/**
 * testGetCabinet method
 *
 * @return void
 */
	public function testGetCabinet() {
		$blockId = 5;
		$roomId = 1;
		$cabinet = $this->Cabinet->getCabinet($blockId, $roomId);
		$this->assertEquals('キャビネット名', $cabinet['Cabinet']['name']);
	}

/**
 * testValidateCabinet method
 *
 * @return void
 */
	public function testValidateCabinet() {
		$data = $this->Cabinet->getNew();
		// validate fail
		$resultFalse = $this->Cabinet->validateCabinet($data);
		$this->assertFalse($resultFalse);

		$data['Cabinet']['key'] = 'new_cabinet_key';
		$data['Cabinet']['block_id'] = 5;
		$data['Cabinet']['name'] = 'New Cabinet';
		//$data['Cabinet']['is_auto_translated'] = false;
		$resultTrue = $this->Cabinet->validateCabinet($data);
		$this->assertTrue($resultTrue);
	}

/**
 * testValidateCabinet method
 *
 * @return void
 */
	public function testValidateCabinetWithModelFail() {
		$data = $this->Cabinet->getNew();
		$data['Cabinet']['key'] = 'new_cabinet_key';
		$data['Cabinet']['block_id'] = 5;
		$data['Cabinet']['name'] = 'New Cabinet';

		$CabinetSettingMock = $this->getMockForModel('Cabinets.CabinetSetting', ['validateCabinetSetting']);
		$CabinetSettingMock->expects($this->once())
			->method('validateCabinetSetting')
			->will($this->returnValue(false));

		$BlockMock = $this->getMockForModel('Blocks.Block', ['validateBlock']);
		$BlockMock->expects($this->once())
			->method('validateBlock')
			->will($this->returnValue(false));

		$CategoryMock = $this->getMockForModel('Categories.Category', ['validateCategories']);
		$CategoryMock->expects($this->once())
			->method('validateCategories')
			->will($this->returnValue(false));

		$this->Cabinet->loadModels([
			'Cabinet' => 'Cabinets.Cabinet',
			'CabinetSetting' => 'Cabinets.CabinetSetting',
			'Category' => 'Categories.Category',
			'Block' => 'Blocks.Block',
			//'Frame' => 'Frames.Frame',
		]);

		$resultFalse = $this->Cabinet->validateCabinet($data, ['cabinetSetting']);
		$this->assertFalse($resultFalse);

		$this->Cabinet->create();
		$resultFalse = $this->Cabinet->validateCabinet($data, ['block']);
		$this->assertFalse($resultFalse);

		$this->Cabinet->create();
		$resultFalse = $this->Cabinet->validateCabinet($data, ['category']);
		$this->assertFalse($resultFalse);
	}

/**
 * testSaveCabinet method
 *
 * @return void
 */
	public function testSaveCabinet() {
		// validate fail
		$data = $this->Cabinet->getNew();
		$data['Cabinet']['key'] = 'new_cabinet_key';
		$data['Cabinet']['block_id'] = 5;
		$data['Cabinet']['name'] = ''; // validate error
		$data['Frame']['id'] = 1;
		$data['CabinetSetting']['cabinet_key'] = 'new_cabinet_key';
		$resultFalse = $this->Cabinet->saveCabinet($data);
		$this->assertFalse($resultFalse);

		$data = $this->Cabinet->getNew();
		$data['Cabinet']['key'] = 'new_cabinet_key';
		$data['Cabinet']['block_id'] = 5;
		$data['Cabinet']['name'] = 'New Cabinet';
		$data['Frame']['id'] = 1;
		$data['CabinetSetting']['cabinet_key'] = 'new_cabinet_key';
		$resultTrue = $this->Cabinet->saveCabinet($data);
		$this->assertTrue($resultTrue);
	}

/**
 * testSaveCabinet InternalErrorException
 *
 * @return void
 */
	public function testSaveCabinetInternalErrorException() {
		// save fail
		$CabinetMock = $this->getMockForModel('Cabinets.Cabinet', ['save']);
		$CabinetMock->expects($this->once())
			->method('save')
			->will($this->returnValue(false));
		$data = $this->Cabinet->getNew();
		$data['Cabinet']['key'] = 'new_cabinet_key';
		$data['Cabinet']['block_id'] = 5;
		$data['Cabinet']['name'] = 'New Cabinet';
		$data['Frame']['id'] = 1;
		$data['CabinetSetting']['cabinet_key'] = 'new_cabinet_key';
		// save失敗で例外
		$this->setExpectedException('InternalErrorException');
		$CabinetMock->saveCabinet($data);
	}

/**
 * testSaveCabinet fail
 *
 * @return void
 */
	public function testSaveCabinetWithModelFail() {
		$CabinetSettingMock = $this->getMockForModel('Cabinets.CabinetSetting', ['save']);
		$CabinetSettingMock->expects($this->once())
			->method('save')
			->will($this->returnValue(false));

		$data = $this->Cabinet->getNew();
		$data['Cabinet']['key'] = 'new_cabinet_key';
		$data['Cabinet']['block_id'] = 5;
		$data['Cabinet']['name'] = 'New Cabinet';
		$data['Frame']['id'] = 1;
		$data['CabinetSetting']['cabinet_key'] = 'new_cabinet_key';
		// CabinetSetting->saveで例外
		$this->setExpectedException('InternalErrorException');
		$this->Cabinet->saveCabinet($data);
	}

}
