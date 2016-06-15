<?php
/**
 * Init
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */


/**
 * Class Init
 */
class Init extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'init';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'cabinet_file_trees' => array(
					'id' => array(
						'type' => 'integer',
						'null' => false,
						'default' => null,
						'unsigned' => false,
						'key' => 'primary',
						'comment' => 'ID | | | '
					),
					'cabinet_key' => array(
						'type' => 'string',
						'null' => false,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'comment' => 'bbs key | キャビネットキー | Hash値 | ',
						'charset' => 'utf8'
					),
					'cabinet_file_key' => array(
						'type' => 'string',
						'null' => false,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'comment' => 'bbs articles key | ファイルキー | Hash値 | ',
						'charset' => 'utf8'
					),
					'cabinet_file_id' => array(
						'type' => 'integer',
						'null' => false,
						'default' => null,
						'unsigned' => false
					),
					'parent_id' => array(
						'type' => 'integer',
						'null' => true,
						'default' => null,
						'unsigned' => false,
						'comment' => 'parent id | 親フォルダのID treeビヘイビア必須カラム | | '
					),
					'lft' => array(
						'type' => 'integer',
						'null' => false,
						'default' => null,
						'unsigned' => false,
						'comment' => 'lft | treeビヘイビア必須カラム | | '
					),
					'rght' => array(
						'type' => 'integer',
						'null' => false,
						'default' => null,
						'unsigned' => false,
						'comment' => 'rght | treeビヘイビア必須カラム | | '
					),
					'created_user' => array(
						'type' => 'integer',
						'null' => true,
						'default' => '0',
						'unsigned' => false,
						'comment' => 'created user | 作成者 | users.id | '
					),
					'created' => array(
						'type' => 'datetime',
						'null' => true,
						'default' => null,
						'comment' => 'created datetime | 作成日時 | | '
					),
					'modified_user' => array(
						'type' => 'integer',
						'null' => true,
						'default' => '0',
						'unsigned' => false,
						'comment' => 'modified user | 更新者 | users.id | '
					),
					'modified' => array(
						'type' => 'datetime',
						'null' => true,
						'default' => null,
						'comment' => 'modified datetime | 更新日時 | | '
					),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array(
						'charset' => 'utf8',
						'collate' => 'utf8_general_ci',
						'engine' => 'InnoDB'
					),
				),
				'cabinet_files' => array(
					'id' => array(
						'type' => 'integer',
						'null' => false,
						'default' => null,
						'unsigned' => false,
						'key' => 'primary',
						'comment' => 'ID |  |  | '
					),
					'cabinet_id' => array(
						'type' => 'integer',
						'null' => false,
						'default' => null,
						'unsigned' => false
					),
					'cabinet_file_tree_parent_id' => array(
						'type' => 'integer',
						'null' => true,
						'default' => null,
						'unsigned' => false
					),
					'status' => array(
						'type' => 'integer',
						'null' => false,
						'default' => null,
						'length' => 4,
						'unsigned' => false,
						'comment' => 'public status, 1: public, 2: public pending, 3: draft during 4: remand | 公開状況  1:公開中、2:公開申請中、3:下書き中、4:差し戻し |  | '
					),
					'is_active' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'is_latest' => array('type' => 'boolean', 'null' => true, 'default' => null),
					'language_id' => array(
						'type' => 'integer',
						'null' => true,
						'default' => null,
						'unsigned' => false
					),
					'filename' => array(
						'type' => 'string',
						'null' => true,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'comment' => 'title | タイトル |  | ',
						'charset' => 'utf8'
					),
					'description' => array(
						'type' => 'text',
						'null' => true,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'comment' => 'file body1 | 本文1 |  | ',
						'charset' => 'utf8'
					),
					'created_user' => array(
						'type' => 'integer',
						'null' => true,
						'default' => '0',
						'unsigned' => false,
						'comment' => 'created user | 作成者 | users.id | '
					),
					'created' => array(
						'type' => 'datetime',
						'null' => true,
						'default' => null,
						'comment' => 'created datetime | 作成日時 |  | '
					),
					'modified_user' => array(
						'type' => 'integer',
						'null' => true,
						'default' => '0',
						'unsigned' => false,
						'comment' => 'modified user | 更新者 | users.id | '
					),
					'modified' => array(
						'type' => 'datetime',
						'null' => true,
						'default' => null,
						'comment' => 'modified datetime | 更新日時 |  | '
					),
					'key' => array(
						'type' => 'string',
						'null' => false,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'charset' => 'utf8'
					),
					'is_folder' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array(
						'charset' => 'utf8',
						'collate' => 'utf8_general_ci',
						'engine' => 'InnoDB'
					),
				),
				'cabinet_settings' => array(
					'id' => array(
						'type' => 'integer',
						'null' => false,
						'default' => null,
						'unsigned' => false,
						'key' => 'primary',
						'comment' => 'ID | | | '
					),
					'cabinet_key' => array(
						'type' => 'string',
						'null' => false,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'comment' => 'Cabinet key | CABINETキー | Hash値 | ',
						'charset' => 'utf8'
					),
					'use_workflow' => array(
						'type' => 'boolean',
						'null' => false,
						'default' => '1',
						'comment' => 'Use workflow, 0:Unused 1:Use | コンテンツの承認機能 0:使わない 1:使う | | '
					),
					'created_user' => array(
						'type' => 'integer',
						'null' => true,
						'default' => '0',
						'unsigned' => false,
						'comment' => 'created user | 作成者 | users.id | '
					),
					'created' => array(
						'type' => 'datetime',
						'null' => true,
						'default' => null,
						'comment' => 'created datetime | 作成日時 | | '
					),
					'modified_user' => array(
						'type' => 'integer',
						'null' => true,
						'default' => '0',
						'unsigned' => false,
						'comment' => 'modified user | 更新者 | users.id | '
					),
					'modified' => array(
						'type' => 'datetime',
						'null' => true,
						'default' => null,
						'comment' => 'modified datetime | 更新日時 | | '
					),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array(
						'charset' => 'utf8',
						'collate' => 'utf8_general_ci',
						'engine' => 'InnoDB'
					),
				),
				'cabinets' => array(
					'id' => array(
						'type' => 'integer',
						'null' => false,
						'default' => null,
						'unsigned' => false,
						'key' => 'primary',
						'comment' => 'ID | | | '
					),
					'block_id' => array(
						'type' => 'integer',
						'null' => false,
						'default' => null,
						'unsigned' => false
					),
					'name' => array(
						'type' => 'string',
						'null' => false,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'comment' => 'CABINET name | CABINET名称 | | ',
						'charset' => 'utf8'
					),
					'key' => array(
						'type' => 'string',
						'null' => false,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'comment' => 'cabinet key | CABINETキー | Hash値 | ',
						'charset' => 'utf8'
					),
					'created_user' => array(
						'type' => 'integer',
						'null' => true,
						'default' => '0',
						'unsigned' => false,
						'comment' => 'created user | 作成者 | users.id | '
					),
					'created' => array(
						'type' => 'datetime',
						'null' => true,
						'default' => null,
						'comment' => 'created datetime | 作成日時 | | '
					),
					'modified_user' => array(
						'type' => 'integer',
						'null' => true,
						'default' => '0',
						'unsigned' => false,
						'comment' => 'modified user | 更新者 | users.id | '
					),
					'modified' => array(
						'type' => 'datetime',
						'null' => true,
						'default' => null,
						'comment' => 'modified datetime | 更新日時 | | '
					),
					'total_size' => array(
						'type' => 'float',
						'null' => true,
						'default' => null,
						'unsigned' => false
					),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array(
						'charset' => 'utf8',
						'collate' => 'utf8_general_ci',
						'engine' => 'InnoDB'
					),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'cabinet_file_trees',
				'cabinet_files',
				'cabinet_settings',
				'cabinets'
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
