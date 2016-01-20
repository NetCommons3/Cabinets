<?php
/**
 * CabinetSettingFixture
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Summary for CabinetSettingFixture
 */
class CabinetSettingFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'comment' => 'ID | | | '),
		'cabinet_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Cabinet key | CABINETキー | Hash値 | ', 'charset' => 'utf8'),
		'use_workflow' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'Use workflow, 0:Unused 1:Use | コンテンツの承認機能 0:使わない 1:使う | | '),
		'use_comment' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'Use of comments, 0:Unused 1:Use | コメント機能 0:使わない 1:使う | | '),
		'use_comment_approval' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'Use of comments approval, 0:Unused 1:Use | コメントの承認機能 0:使わない 1:使う | | '),
		'use_like' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'Use of like button, 0:Unused 1:Use | 高い評価ボタンの使用 0:使わない 1:使う | | '),
		'use_unlike' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'Use of unlike button, 0:Unused 1:Use | 低い評価ボタンの使用 0:使わない 1:使う | | '),
		'use_sns' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => '0', 'comment' => 'created user | 作成者 | users.id | '),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'created datetime | 作成日時 | | '),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => '0', 'comment' => 'modified user | 更新者 | users.id | '),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'modified datetime | 更新日時 | | '),
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
			'cabinet_key' => 'cabinet1',
			'use_workflow' => 1,
			'use_comment' => 1,
			'use_comment_approval' => 1,
			'use_like' => 1,
			'use_unlike' => 1,
			'use_sns' => 1,
			'created_user' => 1,
			'created' => '2015-05-18 01:37:18',
			'modified_user' => 1,
			'modified' => '2015-05-18 01:37:18'
		),
		//array(
		//	'id' => 2,
		//	'cabinet_key' => 'Lorem ipsum dolor sit amet',
		//	'use_workflow' => 1,
		//	'use_comment' => 1,
		//	'use_comment_approval' => 1,
		//	'use_like' => 1,
		//	'use_unlike' => 1,
		//	'use_sns' => 1,
		//	'created_user' => 2,
		//	'created' => '2015-05-18 01:37:18',
		//	'modified_user' => 2,
		//	'modified' => '2015-05-18 01:37:18'
		//),
		//array(
		//	'id' => 3,
		//	'cabinet_key' => 'Lorem ipsum dolor sit amet',
		//	'use_workflow' => 1,
		//	'use_comment' => 1,
		//	'use_comment_approval' => 1,
		//	'use_like' => 1,
		//	'use_unlike' => 1,
		//	'use_sns' => 1,
		//	'created_user' => 3,
		//	'created' => '2015-05-18 01:37:18',
		//	'modified_user' => 3,
		//	'modified' => '2015-05-18 01:37:18'
		//),
		//array(
		//	'id' => 4,
		//	'cabinet_key' => 'Lorem ipsum dolor sit amet',
		//	'use_workflow' => 1,
		//	'use_comment' => 1,
		//	'use_comment_approval' => 1,
		//	'use_like' => 1,
		//	'use_unlike' => 1,
		//	'use_sns' => 1,
		//	'created_user' => 4,
		//	'created' => '2015-05-18 01:37:18',
		//	'modified_user' => 4,
		//	'modified' => '2015-05-18 01:37:18'
		//),
		//array(
		//	'id' => 5,
		//	'cabinet_key' => 'Lorem ipsum dolor sit amet',
		//	'use_workflow' => 1,
		//	'use_comment' => 1,
		//	'use_comment_approval' => 1,
		//	'use_like' => 1,
		//	'use_unlike' => 1,
		//	'use_sns' => 1,
		//	'created_user' => 5,
		//	'created' => '2015-05-18 01:37:18',
		//	'modified_user' => 5,
		//	'modified' => '2015-05-18 01:37:18'
		//),
		//array(
		//	'id' => 6,
		//	'cabinet_key' => 'Lorem ipsum dolor sit amet',
		//	'use_workflow' => 1,
		//	'use_comment' => 1,
		//	'use_comment_approval' => 1,
		//	'use_like' => 1,
		//	'use_unlike' => 1,
		//	'use_sns' => 1,
		//	'created_user' => 6,
		//	'created' => '2015-05-18 01:37:18',
		//	'modified_user' => 6,
		//	'modified' => '2015-05-18 01:37:18'
		//),
		//array(
		//	'id' => 7,
		//	'cabinet_key' => 'Lorem ipsum dolor sit amet',
		//	'use_workflow' => 1,
		//	'use_comment' => 1,
		//	'use_comment_approval' => 1,
		//	'use_like' => 1,
		//	'use_unlike' => 1,
		//	'use_sns' => 1,
		//	'created_user' => 7,
		//	'created' => '2015-05-18 01:37:18',
		//	'modified_user' => 7,
		//	'modified' => '2015-05-18 01:37:18'
		//),
		//array(
		//	'id' => 8,
		//	'cabinet_key' => 'Lorem ipsum dolor sit amet',
		//	'use_workflow' => 1,
		//	'use_comment' => 1,
		//	'use_comment_approval' => 1,
		//	'use_like' => 1,
		//	'use_unlike' => 1,
		//	'use_sns' => 1,
		//	'created_user' => 8,
		//	'created' => '2015-05-18 01:37:18',
		//	'modified_user' => 8,
		//	'modified' => '2015-05-18 01:37:18'
		//),
		//array(
		//	'id' => 9,
		//	'cabinet_key' => 'Lorem ipsum dolor sit amet',
		//	'use_workflow' => 1,
		//	'use_comment' => 1,
		//	'use_comment_approval' => 1,
		//	'use_like' => 1,
		//	'use_unlike' => 1,
		//	'use_sns' => 1,
		//	'created_user' => 9,
		//	'created' => '2015-05-18 01:37:18',
		//	'modified_user' => 9,
		//	'modified' => '2015-05-18 01:37:18'
		//),
		//array(
		//	'id' => 10,
		//	'cabinet_key' => 'Lorem ipsum dolor sit amet',
		//	'use_workflow' => 1,
		//	'use_comment' => 1,
		//	'use_comment_approval' => 1,
		//	'use_like' => 1,
		//	'use_unlike' => 1,
		//	'use_sns' => 1,
		//	'created_user' => 10,
		//	'created' => '2015-05-18 01:37:18',
		//	'modified_user' => 10,
		//	'modified' => '2015-05-18 01:37:18'
		//),
	);

}
