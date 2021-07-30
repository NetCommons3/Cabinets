<?php
/**
 * CabinetFileTree::save()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('CabinetFileTreeFixture', 'Cabinets.Test/Fixture');

/**
 * CabinetFileTree::save()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Model\CabinetFileTree
 */
class CabinetFileTreeSaveTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet',
		'plugin.cabinets.cabinet_file',
		'plugin.cabinets.cabinet_file_tree',
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
	protected $_modelName = 'CabinetFileTree';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'save';

/**
 * Saveのテスト
 *
 * @return void
 */
	public function testSave() {
		// save時に必ずmodifiedフィールドが更新されるようにunsetする
		$data = (new CabinetFileTreeFixture())->records[0];
		$data['modified'] = '2000-01-01 00:00:00';

		$this->CabinetFileTree->save($data);
		$this->assertNotEquals(
			$this->CabinetFileTree->data['CabinetFileTree']['modified'],
			$data['modified']
		);
	}
}
