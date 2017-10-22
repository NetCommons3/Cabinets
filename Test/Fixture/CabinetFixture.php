<?php
/**
 * CabinetFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Your Name <yourname@domain.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for CabinetFixture
 */
class CabinetFixture extends CakeTestFixture {

/**
 * Records id1-2は予約
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 2,
			'block_id' => '2',
			'name' => 'Cabinet2',
			'key' => 'content_block_1',
			'created_user' => 1,
			'created' => '2016-04-14 02:49:44',
			'modified_user' => 1,
			'modified' => '2016-04-14 02:49:44',
			'total_size' => '1'
		),
		array(
			'id' => 4,
			'block_id' => '4',
			'name' => 'Cabinet4',
			'key' => 'content_block_2',
			'created_user' => 2,
			'created' => '2016-04-14 02:49:44',
			'modified_user' => 2,
			'modified' => '2016-04-14 02:49:44',
			'total_size' => '1'
		),
		// この上までテストテンプレの予約
		array(
			'id' => 3,
			'block_id' => 3,
			'name' => 'Cabinet3',
			'key' => 'cabinet_3',
			'created_user' => 1,
			'created' => '2016-04-14 02:49:44',
			'modified_user' => 1,
			'modified' => '2016-04-14 02:49:44',
			'total_size' => 1
		),
	);

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		require_once App::pluginPath('Cabinets') . 'Config' . DS . 'Schema' . DS . 'schema.php';
		$this->fields = (new CabinetsSchema())->tables[Inflector::tableize($this->name)];
		parent::init();
	}

}
