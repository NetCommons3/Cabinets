<?php
/**
 * CabinetFrameSetting Test Case
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('CabinetFrameSetting', 'Cabinets.Model');

/**
 * Summary for CabinetFrameSetting Test Case
 *
 * @property CabinetFrameSetting $CabinetFrameSetting
 */
class CabinetFrameSettingTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet_frame_setting',
		'plugin.users.user', // Trackableビヘイビアでテーブルが必用
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->CabinetFrameSetting = ClassRegistry::init('Cabinets.CabinetFrameSetting');
		// モデルからビヘイビアをはずす:
		$this->CabinetFrameSetting->Behaviors->unload('Trackable');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CabinetFrameSetting);

		parent::tearDown();
	}

/**
 * test getSettingByFrameKey
 *
 * @return void
 */
	public function testGetSettingByFrameKey() {
		$frameSetting = $this->CabinetFrameSetting->getSettingByFrameKey('frame_1');
		$this->assertEquals('frame_1', $frameSetting['frame_key']);
	}

/**
 * test getSettingByFrameKey データがなければ作成される
 *
 * @return void
 */
	public function testGetSettingByFrameKeyNotFound() {
		$frameSetting = $this->CabinetFrameSetting->getSettingByFrameKey('frame_key_not_found');
		$this->assertEquals('frame_key_not_found', $frameSetting['frame_key']);
		$this->assertTrue($frameSetting['id'] > 0);
	}

/**
 * test saveCabinetFrameSetting
 *
 * @return void
 */
	public function testSaveCabinetFrameSetting() {
		$data = $this->CabinetFrameSetting->getNew();
		// バリデート失敗
		$resultFalse = $this->CabinetFrameSetting->saveCabinetFrameSetting($data);
		$this->assertFalse($resultFalse);

		// 保存成功
		$data['CabinetFrameSetting']['frame_key'] = 'frame_key';
		$savedData = $this->CabinetFrameSetting->saveCabinetFrameSetting($data);
		$this->assertTrue(($savedData['CabinetFrameSetting']['id'] > 0));
	}

/**
 * test saveCabinetFrameSetting save失敗で例外投げられるテスト
 *
 * @return void
 */
	public function testSaveCabinetFrameSettingSaveFailed() {
		$data = $this->CabinetFrameSetting->getNew();
		$CabinetFrameSettingMock = $this->getMockForModel('Cabinets.CabinetFrameSetting', ['save']);
		$CabinetFrameSettingMock->expects($this->once())
			->method('save')
			->will($this->returnValue(false));
		// save fail
		$data['CabinetFrameSetting']['frame_key'] = 'frame_key';
		$this->setExpectedException('InternalErrorException');
		$CabinetFrameSettingMock->saveCabinetFrameSetting($data);
	}

/**
 * test getDisplayNumberOptions
 *
 * @return void
 */
	public function testGetDisplayNumberOptions() {
		$array = $this->CabinetFrameSetting->getDisplayNumberOptions();
		$this->assertInternalType('array', $array);
	}
}
