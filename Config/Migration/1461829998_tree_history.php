<?php

class TreeHistory extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'tree_history';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'cabinet_files' => array(
					'cabinet_file_tree_parent_id' => array(
						'type' => 'integer',
						'null' => true,
						'default' => null,
						'unsigned' => false,
						'after' => 'cabinet_id'
					),
				),
			),
			'drop_field' => array(
				'cabinet_files' => array('parent_id'),
			),
		),
		'down' => array(
			'drop_field' => array(
				'cabinet_files' => array('cabinet_file_tree_parent_id'),
			),
			'create_field' => array(
				'cabinet_files' => array(
					'parent_id' => array(
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
