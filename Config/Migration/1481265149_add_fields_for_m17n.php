<?php
/**
 * 多言語化対応
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * 多言語化対応
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Cabinets\Config\Migration
 */
class AddFieldsForM17n extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_fields_for_m17n';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'cabinet_files' => array(
					'cabinet_key' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'キャビネットキー', 'charset' => 'utf8', 'after' => 'cabinet_id'),
					'cabinet_file_tree_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'after' => 'cabinet_file_tree_parent_id'),
					'is_origin' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'オリジナルかどうか', 'after' => 'language_id'),
					'is_translation' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '翻訳したかどうか', 'after' => 'is_origin'),
				),
				'cabinets' => array(
					'language_id' => array('type' => 'integer', 'null' => true, 'default' => '2', 'length' => 6, 'unsigned' => false, 'comment' => '言語ID', 'after' => 'block_id'),
					'is_origin' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'オリジナルかどうか', 'after' => 'language_id'),
					'is_translation' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '翻訳したかどうか', 'after' => 'is_origin'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'cabinet_files' => array('cabinet_key', 'cabinet_file_tree_id', 'is_origin', 'is_translation'),
				'cabinets' => array('language_id', 'is_origin', 'is_translation'),
			),
			'drop_table' => array(
				'cabinet_files_languages'
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
