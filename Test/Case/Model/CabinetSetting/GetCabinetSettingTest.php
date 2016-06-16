<?php
/**
 * CabinetSetting::getCabinetSetting()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');

/**
 * CabinetSetting::getCabinetSetting()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Model\CabinetSetting
 */
class CabinetSettingGetCabinetSettingTest extends NetCommonsGetTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet',
		'plugin.cabinets.cabinet_file',
		'plugin.cabinets.cabinet_file_tree',
		'plugin.cabinets.cabinet_setting',
		'plugin.workflow.workflow_comment',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'cabinets';

/**
 * Model name
 *
 * @var string
 */
	protected $_modelName = 'CabinetSetting';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getCabinetSetting';

/**
 * getCabinetSetting()のテスト
 *
 * @return void
 */
	public function testGetCabinetSetting() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$cabinetKey = 'content_block_1';

		//テスト実施
		$result = $this->$model->$methodName($cabinetKey);

		$this->assertInternalType('array', $result);
		$this->assertEquals(2, $result['CabinetSetting']['id']);
	}

}
