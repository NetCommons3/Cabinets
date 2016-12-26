<?php
/**
 * CabinetFileTreeFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Your Name <yourname@domain.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for CabinetFileTreeFixture
 */
class CabinetFileTreeFixture extends CakeTestFixture {

/**
 * Records CabinetFile.id 1-8に対応するレコードは予約
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'cabinet_key' => 'Lorem ipsum dolor sit amet',
			'cabinet_file_key' => 'Lorem ipsum dolor sit amet',
			'parent_id' => 1,
			'lft' => 1,
			'rght' => 1,
			'created_user' => 1,
			'created' => '2016-04-14 02:48:19',
			'modified_user' => 1,
			'modified' => '2016-04-14 02:48:19'
		),
		array(
			'id' => 10,
			'cabinet_key' => 'cabinet_3',
			'cabinet_file_key' => 'content_key_10',
			'parent_id' => null,
			'lft' => 1,
			'rght' => 2,
			'created_user' => 1,
			'created' => '2016-04-14 02:48:19',
			'modified_user' => 1,
			'modified' => '2016-04-14 02:48:19'
		),
		array(
			'id' => 11,
			'cabinet_key' => 'content_block_1',
			'cabinet_file_key' => 'content_key_11',
			'parent_id' => null,
			'lft' => 3,
			'rght' => 40,
			'created_user' => 1,
			'created' => '2016-04-14 02:48:19',
			'modified_user' => 1,
			'modified' => '2016-04-14 02:48:19'
		),
		array(
			'id' => 12,
			'cabinet_key' => 'content_block_1',
			'cabinet_file_key' => 'content_key_1',
			'parent_id' => 11,
			'lft' => 4,
			'rght' => 5,
			'created_user' => 1,
			'created' => '2016-04-14 02:48:19',
			'modified_user' => 1,
			'modified' => '2016-04-14 02:48:19'
		),
		array(
			'id' => 13,
			'cabinet_key' => 'content_block_1',
			'cabinet_file_key' => 'content_key_1',
			'parent_id' => 11,
			'lft' => 6,
			'rght' => 7,
			'created_user' => 1,
			'created' => '2016-04-14 02:48:19',
			'modified_user' => 1,
			'modified' => '2016-04-14 02:48:19'
		),
		array(
			'id' => 14,
			'cabinet_key' => 'content_block_1',
			'cabinet_file_key' => 'content_key_2',
			'parent_id' => 11,
			'lft' => 8,
			'rght' => 9,
			'created_user' => 4,
			'created' => '2016-04-14 02:48:19',
			'modified_user' => 4,
			'modified' => '2016-04-14 02:48:19'
		),
		// DownloadFolderTest
		// folder
		array(
			'id' => 15,
			'cabinet_key' => 'content_block_1',
			'cabinet_file_key' => 'content_key_12',
			'parent_id' => 11,
			'lft' => 10,
			'rght' => 15,
			'created_user' => 1,
			'created' => '2016-04-14 02:48:19',
			'modified_user' => 1,
			'modified' => '2016-04-14 02:48:19'
		),
		// file
		array(
			'id' => 16,
			'cabinet_key' => 'content_block_1',
			'cabinet_file_key' => 'content_key_13',
			'parent_id' => 15,
			'lft' => 12,
			'rght' => 13,
			'created_user' => 1,
			'created' => '2016-04-14 02:48:19',
			'modified_user' => 1,
			'modified' => '2016-04-14 02:48:19'
		),
		// folder
		array(
			'id' => 17,
			'cabinet_key' => 'content_block_1',
			'cabinet_file_key' => 'content_key_14',
			'parent_id' => 15,
			'lft' => 11,
			'rght' => 14,
			'created_user' => 1,
			'created' => '2016-04-14 02:48:19',
			'modified_user' => 1,
			'modified' => '2016-04-14 02:48:19'
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
