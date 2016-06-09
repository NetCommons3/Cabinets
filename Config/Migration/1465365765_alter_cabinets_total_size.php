<?php
class AlterCabinetsTotalSize extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_cabinets_total_size';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'cabinets' => array(
					'total_size' => array('type' => 'float', 'null' => false, 'default' => '0', 'unsigned' => false),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'cabinets' => array(
					'total_size' => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false),
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
