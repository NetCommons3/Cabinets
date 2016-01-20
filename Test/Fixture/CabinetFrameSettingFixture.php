<?php
/**
 * CabinetFrameSettingFixture
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Summary for CabinetFrameSettingFixture
 */
class CabinetFrameSettingFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'comment' => 'ID |  |  | '),
		'frame_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'frame key | フレームKey | frames.key | ', 'charset' => 'utf8'),
		'posts_per_page' => array('type' => 'integer', 'null' => false, 'default' => '10', 'comment' => 'display number | 表示件数 |  | '),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => 'created user | 作成者 | users.id | '),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'created datetime | 作成日時 |  | '),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => 'modified user | 更新者 | users.id | '),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'modified datetime | 更新日時 |  | '),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'frame_key' => 'frame_1',
			'posts_per_page' => 1,
			'created_user' => 1,
			'created' => '2015-05-18 01:37:38',
			'modified_user' => 1,
			'modified' => '2015-05-18 01:37:38'
		),
		//array(
		//	'id' => 2,
		//	'frame_key' => 'Lorem ipsum dolor sit amet',
		//	'posts_per_page' => 2,
		//	'created_user' => 2,
		//	'created' => '2015-05-18 01:37:38',
		//	'modified_user' => 2,
		//	'modified' => '2015-05-18 01:37:38'
		//),
		//array(
		//	'id' => 3,
		//	'frame_key' => 'Lorem ipsum dolor sit amet',
		//	'posts_per_page' => 3,
		//	'created_user' => 3,
		//	'created' => '2015-05-18 01:37:38',
		//	'modified_user' => 3,
		//	'modified' => '2015-05-18 01:37:38'
		//),
		//array(
		//	'id' => 4,
		//	'frame_key' => 'Lorem ipsum dolor sit amet',
		//	'posts_per_page' => 4,
		//	'created_user' => 4,
		//	'created' => '2015-05-18 01:37:38',
		//	'modified_user' => 4,
		//	'modified' => '2015-05-18 01:37:38'
		//),
		//array(
		//	'id' => 5,
		//	'frame_key' => 'Lorem ipsum dolor sit amet',
		//	'posts_per_page' => 5,
		//	'created_user' => 5,
		//	'created' => '2015-05-18 01:37:38',
		//	'modified_user' => 5,
		//	'modified' => '2015-05-18 01:37:38'
		//),
		//array(
		//	'id' => 6,
		//	'frame_key' => 'Lorem ipsum dolor sit amet',
		//	'posts_per_page' => 6,
		//	'created_user' => 6,
		//	'created' => '2015-05-18 01:37:38',
		//	'modified_user' => 6,
		//	'modified' => '2015-05-18 01:37:38'
		//),
		//array(
		//	'id' => 7,
		//	'frame_key' => 'Lorem ipsum dolor sit amet',
		//	'posts_per_page' => 7,
		//	'created_user' => 7,
		//	'created' => '2015-05-18 01:37:38',
		//	'modified_user' => 7,
		//	'modified' => '2015-05-18 01:37:38'
		//),
		//array(
		//	'id' => 8,
		//	'frame_key' => 'Lorem ipsum dolor sit amet',
		//	'posts_per_page' => 8,
		//	'created_user' => 8,
		//	'created' => '2015-05-18 01:37:38',
		//	'modified_user' => 8,
		//	'modified' => '2015-05-18 01:37:38'
		//),
		//array(
		//	'id' => 9,
		//	'frame_key' => 'Lorem ipsum dolor sit amet',
		//	'posts_per_page' => 9,
		//	'created_user' => 9,
		//	'created' => '2015-05-18 01:37:38',
		//	'modified_user' => 9,
		//	'modified' => '2015-05-18 01:37:38'
		//),
		//array(
		//	'id' => 10,
		//	'frame_key' => 'Lorem ipsum dolor sit amet',
		//	'posts_per_page' => 10,
		//	'created_user' => 10,
		//	'created' => '2015-05-18 01:37:38',
		//	'modified_user' => 10,
		//	'modified' => '2015-05-18 01:37:38'
		//),
	);

}
