<?php
/**
 * CabinetFileTree::beforeFind()とafterFind()のテスト
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
 * CabinetFileTree::beforeFind()とafterFind()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Model\CabinetFileTree
 */
class CabinetFileTreeFindTest extends NetCommonsModelTestCase {

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
	protected $_methodName = 'find';

/**
 * find()のテスト
 *
 * @return void
 */
	public function testFind() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		//$data['CabinetFileTree'] = (new CabinetFileTreeFixture())->records[0];

		$options = [
			'conditions' => [
				//'1' => '1'
			]
		];
		//テスト実施
		$result = $this->$model->$methodName('all', $options);

		// ゲストでFindしたらCabinetFile.is_active = 1のデータしか取得できないはず
		debug($result);
		foreach($result as $cabinetFileTree) {
			$this->assertEquals($cabinetFileTree['CabinetFile']['is_active'], 1);
		}
		//チェック
		//TODO:Assertを書く
	}

}
