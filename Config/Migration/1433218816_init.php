<?php
/**
 * Init
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
				'cabinet_files' => array(
					'id' => array(
						'type' => 'integer',
						'null' => false,
						'default' => null,
						'unsigned' => false,
						'key' => 'primary',
						'comment' => 'ID |  |  | '
					),
					'cabinet_key' => array(
						'type' => 'string',
						'null' => false,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'charset' => 'utf8'
					),
					'origin_id' => array(
						'type' => 'string',
						'null' => false,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'comment' => 'file key | エントリーキー | Hash値 | ',
						'charset' => 'utf8'
					),
					'category_id' => array(
						'type' => 'integer',
						'null' => false,
						'default' => null,
						'unsigned' => false,
						'comment' => 'category id | カテゴリーID | cabinet_categories.id | '
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
					'plus_vote_number' => array(
						'type' => 'integer',
						'null' => false,
						'default' => '0',
						'unsigned' => false,
						'comment' => 'plus vote number | プラス投票数 |  | '
					),
					'minus_vote_number' => array(
						'type' => 'integer',
						'null' => false,
						'default' => '0',
						'unsigned' => false,
						'comment' => 'minus vote number | マイナス投票数 |  | '
					),
					'published_datetime' => array(
						'type' => 'datetime',
						'null' => false,
						'default' => null
					),
					'is_auto_translated' => array(
						'type' => 'boolean',
						'null' => false,
						'default' => '0',
						'comment' => 'translation type. 0:original , 1:auto translation | 翻訳タイプ  0:オリジナル、1:自動翻訳 |  | '
					),
					'translation_engine' => array(
						'type' => 'string',
						'null' => true,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'comment' => 'translation engine | 翻訳エンジン |  | ',
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
					'block_id' => array(
						'type' => 'integer',
						'null' => true,
						'default' => null,
						'unsigned' => false
					),
					'key' => array(
						'type' => 'string',
						'null' => false,
						'collate' => 'utf8_general_ci',
						'charset' => 'utf8'
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
				'cabinet_frame_settings' => array(
					'id' => array(
						'type' => 'integer',
						'null' => false,
						'default' => null,
						'unsigned' => false,
						'key' => 'primary',
						'comment' => 'ID |  |  | '
					),
					'frame_key' => array(
						'type' => 'string',
						'null' => false,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'comment' => 'frame key | フレームKey | frames.key | ',
						'charset' => 'utf8'
					),
					'posts_per_page' => array(
						'type' => 'integer',
						'null' => false,
						'default' => '10',
						'unsigned' => false,
						'comment' => 'display number | 表示件数 |  | '
					),
					'created_user' => array(
						'type' => 'integer',
						'null' => true,
						'default' => null,
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
						'default' => null,
						'unsigned' => false,
						'comment' => 'modified user | 更新者 | users.id | '
					),
					'modified' => array(
						'type' => 'datetime',
						'null' => true,
						'default' => null,
						'comment' => 'modified datetime | 更新日時 |  | '
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
					'use_comment' => array(
						'type' => 'boolean',
						'null' => false,
						'default' => '1',
						'comment' => 'Use of comments, 0:Unused 1:Use | コメント機能 0:使わない 1:使う | | '
					),
					'use_comment_approval' => array(
						'type' => 'boolean',
						'null' => false,
						'default' => '1',
						'comment' => 'Use of comments approval, 0:Unused 1:Use | コメントの承認機能 0:使わない 1:使う | | '
					),
					'use_like' => array(
						'type' => 'boolean',
						'null' => false,
						'default' => '1',
						'comment' => 'Use of like button, 0:Unused 1:Use | 高い評価ボタンの使用 0:使わない 1:使う | | '
					),
					'use_unlike' => array(
						'type' => 'boolean',
						'null' => false,
						'default' => '1',
						'comment' => 'Use of unlike button, 0:Unused 1:Use | 低い評価ボタンの使用 0:使わない 1:使う | | '
					),
					'use_sns' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
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
					'is_auto_translated' => array(
						'type' => 'boolean',
						'null' => false,
						'default' => '0',
						'comment' => 'translation type. 0:original , 1:auto translation | 翻訳タイプ 0:オリジナル、1:自動翻訳 | | '
					),
					'translation_engine' => array(
						'type' => 'string',
						'null' => true,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'comment' => 'translation engine | 翻訳エンジン | | ',
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
				'cabinet_files',
				'cabinet_frame_settings',
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
