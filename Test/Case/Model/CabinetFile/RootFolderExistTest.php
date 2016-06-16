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
class CabinetFileRootFolderExistTest extends WorkflowGetTest {

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
		'plugin.categories.category',
		'plugin.categories.category_order',
		'plugin.workflow.workflow_comment',
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
	protected $_methodName = 'rootFolderExist';

/**
 * rootFolderExist()のテスト
 *
 * @return void
 */
	public function testRootFolderExist() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$cabinet = [
			'Cabinet' => [
				'key' => 'cabinet_3',
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
	public function testRootFolderNotExist() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$cabinet = [
			'Cabinet' => [
				'key' => 'cabinet_4',
			],
		];

		//テスト実施
		$result = $this->$model->$methodName($cabinet);

		$this->assertFalse($result);
	}
}
