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
class CabinetDeleteTest extends CabinetsAppModelTestBase {

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
 * testDeleteCabinet method
 *
 * @return void
 */
	public function testDeleteCabinet() {
		$data = $this->Cabinet->findById(1);
		$resultTrue = $this->Cabinet->deleteCabinet($data);
		$this->assertTrue($resultTrue);
	}

/**
 * testDeleteCabinet method
 *
 * @return void
 */
	public function testDeleteCabinetInternalErrorException() {
		$CabinetMock = $this->getMockForModel('Cabinets.Cabinet', ['deleteAll']);
		$CabinetMock->expects($this->once())
			->method('deleteAll')
			->will($this->returnValue(false));

		$data = $this->Cabinet->findById(1);
		$this->setExpectedException('InternalErrorException');
		$CabinetMock->deleteCabinet($data);
	}

/**
 * testDeleteCabinet method
 *
 * @return void
 */
	public function testDeleteCabinetWithCabinetSettingInternalErrorException() {
		$CabinetSettingMock = $this->getMockForModel('Cabinets.CabinetSetting', ['deleteAll']);
		$CabinetSettingMock->expects($this->once())
			->method('deleteAll')
			->will($this->returnValue(false));

		$data = $this->Cabinet->findById(1);
		$this->setExpectedException('InternalErrorException');
		$this->Cabinet->deleteCabinet($data);
	}

/**
 * testDeleteCabinet method
 *
 * @return void
 */
	public function testDeleteCabinetWithCabinetFileInternalErrorException() {
		$CabinetFileMock = $this->getMockForModel('Cabinets.CabinetFile', ['deleteAll']);
		$CabinetFileMock->expects($this->once())
			->method('deleteAll')
			->will($this->returnValue(false));

		$data = $this->Cabinet->findById(1);
		$this->setExpectedException('InternalErrorException');
		$this->Cabinet->deleteCabinet($data);
	}

}
