<?php
/**
 * AddUseAuthKey
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */


/**
 * Class AddUseAuthKey
 */
class AddUseAuthKey extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_use_auth_key';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'cabinet_files' => array(
					'use_auth_key' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'is_folder'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'cabinet_files' => array('use_auth_key'),
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
