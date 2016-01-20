<?php
/**
 * Karuizawa
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Class Karuizawa
 */
class Karuizawa extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'karuizawa';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'cabinet_frame_settings' => array(
					'articles_per_page' => array('type' => 'integer', 'null' => false, 'default' => '10', 'unsigned' => false, 'comment' => 'display number | 表示件数 |  | ', 'after' => 'frame_key'),
				),
			),
			'drop_field' => array(
				'cabinet_frame_settings' => array('posts_per_page'),
			),
		),
		'down' => array(
			'drop_field' => array(
				'cabinet_frame_settings' => array('articles_per_page'),
			),
			'create_field' => array(
				'cabinet_frame_settings' => array(
					'posts_per_page' => array('type' => 'integer', 'null' => false, 'default' => '10', 'unsigned' => false, 'comment' => 'display number | 表示件数 |  | '),
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
