<?php

class DeleteNotUses extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'delete_not_uses';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'cabinet_files' => array(
					'plus_vote_number' => array(
						'type' => 'integer',
						'null' => false,
						'default' => '0',
						'unsigned' => false,
						'comment' => 'plus vote number | プラス投票数 |  | ',
						'after' => 'description'
					),
					'minus_vote_number' => array(
						'type' => 'integer',
						'null' => false,
						'default' => '0',
						'unsigned' => false,
						'comment' => 'minus vote number | マイナス投票数 |  | ',
						'after' => 'plus_vote_number'
					),
				),
			),
			'drop_field' => array(
				'cabinet_settings' => array(
					'use_comment',
					'use_comment_approval',
					'use_like',
					'use_unlike',
					'use_sns'
				),
			),
			'drop_table' => array(
				'cabinet_frame_settings'
			),
		),
		'down' => array(
			'drop_field' => array(
				'cabinet_files' => array('plus_vote_number', 'minus_vote_number'),
			),
			'create_field' => array(
				'cabinet_settings' => array(
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
				),
			),
			'create_table' => array(
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
					'articles_per_page' => array(
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
