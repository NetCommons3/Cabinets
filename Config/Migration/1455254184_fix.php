<?php

class Fix extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'fix';

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
			),
			'create_field' => array(
				'cabinet_files' => array(
					'cabinet_id' => array(
						'type' => 'integer',
						'null' => false,
						'default' => null,
						'unsigned' => false,
						'after' => 'id'
					),
					'filename' => array(
						'type' => 'string',
						'null' => true,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'comment' => 'title | タイトル |  | ',
						'charset' => 'utf8',
						'after' => 'language_id'
					),
					'description' => array(
						'type' => 'text',
						'null' => true,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'comment' => 'file body1 | 本文1 |  | ',
						'charset' => 'utf8',
						'after' => 'filename'
					),
					'is_folder' => array(
						'type' => 'boolean',
						'null' => false,
						'default' => '0',
						'after' => 'key'
					),
				),
				'cabinets' => array(
					'size' => array(
						'type' => 'integer',
						'null' => false,
						'default' => '0',
						'unsigned' => false,
						'after' => 'modified'
					),
				),
			),
			'drop_field' => array(
				'cabinet_files' => array(
					'cabinet_key',
					'category_id',
					'title',
					'body1',
					'body2',
					'public_type',
					'publish_start',
					'publish_end',
					'block_id'
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'cabinet_file_trees'
			),
			'drop_field' => array(
				'cabinet_files' => array('cabinet_id', 'filename', 'description', 'is_folder'),
				'cabinets' => array('size'),
			),
			'create_field' => array(
				'cabinet_files' => array(
					'cabinet_key' => array(
						'type' => 'string',
						'null' => false,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'charset' => 'utf8'
					),
					'category_id' => array(
						'type' => 'integer',
						'null' => false,
						'default' => null,
						'unsigned' => false,
						'comment' => 'category id | カテゴリーID | cabinet_categories.id | '
					),
					'title' => array(
						'type' => 'string',
						'null' => true,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'comment' => 'title | タイトル |  | ',
						'charset' => 'utf8'
					),
					'body1' => array(
						'type' => 'text',
						'null' => true,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'comment' => 'file body1 | 本文1 |  | ',
						'charset' => 'utf8'
					),
					'body2' => array(
						'type' => 'text',
						'null' => true,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'comment' => 'file body2 | 本文2 |  | ',
						'charset' => 'utf8'
					),
					'public_type' => array(
						'type' => 'integer',
						'null' => false,
						'default' => '1',
						'length' => 4,
						'unsigned' => false
					),
					'publish_start' => array(
						'type' => 'datetime',
						'null' => true,
						'default' => null
					),
					'publish_end' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'block_id' => array(
						'type' => 'integer',
						'null' => true,
						'default' => null,
						'unsigned' => false
					),
				),
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
