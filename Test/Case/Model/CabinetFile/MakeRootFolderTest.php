<?php
/**
 * CabinetFile::rootFolderExist()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowGetTest', 'Workflow.TestSuite');

/**
 * CabinetFile::rootFolderExist()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Model\CabinetFile
 */
class CabinetFileMakeRootFolderTest extends WorkflowGetTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet',
		'plugin.cabinets.cabinet_file',
		'plugin.cabinets.cabinet_file_tree',
		'plugin.cabinets.block_setting_for_cabinet',
		'plugin.categories.category',
		'plugin.categories.category_order',
		'plugin.workflow.workflow_comment',
		'plugin.authorization_keys.authorization_keys',
		'plugin.site_manager.site_setting',
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
	protected $_modelName = 'CabinetFile';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'makeRootFolder';

/**
 * rootFolderExist()のテスト
 *
 * @return void
 */
	public function testMakeRootFolderWithRootFolderExist() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$cabinet = [
			'Cabinet' => [
				'id' => 3,
				'key' => 'cabinet_3',
				'name' => 'Cabinet3',
			],
		];

		//テスト実施
		$result = $this->$model->$methodName($cabinet);

		$this->assertTrue($result);
	}

/**
 * rootFolderExist()のテスト
 *
 * @return void
 */
	public function testNewMakeRootFolder() {
		Current::$current['Block']['id'] = 10;

		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$cabinet = [
			'Cabinet' => [
				'id' => 4,
				'key' => 'cabinet_4',
				'name' => 'Cabinet4',
			],
		];

		//テスト実施
		$result = $this->$model->$methodName($cabinet);

		$this->assertTrue($result);
	}
}
