<?php
/**
 * DropOriginId
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Class DropOriginId
 */
class DropOriginId extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'drop_origin_id';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'drop_field' => array(
				'cabinet_files' => array('origin_id'),
			),
		),
		'down' => array(
			'create_field' => array(
				'cabinet_files' => array(
					'origin_id' => array(
						'type' => 'string',
						'null' => false,
						'default' => null,
						'collate' => 'utf8_general_ci',
						'comment' => 'file key | エントリーキー | Hash値 | ',
						'charset' => 'utf8'
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
