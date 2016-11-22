<?php
/**
 * AddIndex
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Class AddIndex
 */
class AddIndex extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_index';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'cabinet_file_trees' => array(
					'cabinet_key' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'キャビネットキー', 'charset' => 'utf8'),
					'cabinet_file_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '親フォルダのID treeビヘイビア必須カラム'),
					'lft' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => 'lft  treeビヘイビア必須カラム'),
				),
				'cabinet_files' => array(
					'key' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'cabinet_settings' => array(
					'cabinet_key' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'キャビネットキー', 'charset' => 'utf8'),
				),
				'cabinets' => array(
					'block_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
				),
			),
			'create_field' => array(
				'cabinet_file_trees' => array(
					'indexes' => array(
						'parent_id' => array('column' => 'parent_id', 'unique' => 0),
						'cabinet_key' => array('column' => array('cabinet_key', 'lft', 'rght'), 'unique' => 0),
						'lft' => array('column' => array('lft', 'rght'), 'unique' => 0),
						'cabinet_file_id' => array('column' => 'cabinet_file_id', 'unique' => 0),
					),
				),
				'cabinet_files' => array(
					'indexes' => array(
						'key' => array('column' => array('key', 'language_id'), 'unique' => 0),
					),
				),
				'cabinet_settings' => array(
					'indexes' => array(
						'cabinet_key' => array('column' => 'cabinet_key', 'unique' => 0),
					),
				),
				'cabinets' => array(
					'indexes' => array(
						'block_id' => array('column' => 'block_id', 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'cabinet_file_trees' => array(
					'cabinet_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'キャビネットキー', 'charset' => 'utf8'),
					'cabinet_file_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
					'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '親フォルダのID treeビヘイビア必須カラム'),
					'lft' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'lft  treeビヘイビア必須カラム'),
				),
				'cabinet_files' => array(
					'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'cabinet_settings' => array(
					'cabinet_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'キャビネットキー', 'charset' => 'utf8'),
				),
				'cabinets' => array(
					'block_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
				),
			),
			'drop_field' => array(
				'cabinet_file_trees' => array('indexes' => array('parent_id', 'cabinet_key', 'lft', 'cabinet_file_id')),
				'cabinet_files' => array('indexes' => array('key')),
				'cabinet_settings' => array('indexes' => array('cabinet_key')),
				'cabinets' => array('indexes' => array('block_id')),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		return true;
	}
}