<?php

class DeleteNotUsedFields extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'delete_not_used_fields';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'drop_field' => array(
				'cabinet_files' => array('plus_vote_number', 'minus_vote_number'),
				'cabinets' => array('size'),
			),
			'create_field' => array(
				'cabinets' => array(
					'total_size' => array(
						'type' => 'float',
						'null' => true,
						'default' => null,
						'unsigned' => false,
						'after' => 'modified'
					),
				),
			),
		),
		'down' => array(
			'create_field' => array(
				'cabinet_files' => array(
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
				),
				'cabinets' => array(
					'size' => array(
						'type' => 'integer',
						'null' => false,
						'default' => '0',
						'unsigned' => false
					),
				),
			),
			'drop_field' => array(
				'cabinets' => array('total_size'),
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
