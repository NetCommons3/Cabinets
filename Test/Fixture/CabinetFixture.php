<?php
/**
 * CabinetFixture
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Summary for CabinetFixture
 */
class CabinetFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'comment' => 'ID | | | '),
		'block_id' => array('type' => 'integer', 'null' => false, 'default' => null),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'CABINET name | CABINET名称 | | ', 'charset' => 'utf8'),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'cabinet key | CABINETキー | Hash値 | ', 'charset' => 'utf8'),
		'is_auto_translated' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'translation type. 0:original , 1:auto translation | 翻訳タイプ 0:オリジナル、1:自動翻訳 | | '),
		'translation_engine' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'translation engine | 翻訳エンジン | | ', 'charset' => 'utf8'),
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
			'block_id' => 5,
			'name' => 'キャビネット名',
			'key' => 'cabinet1',
			'is_auto_translated' => 1,
			'translation_engine' => 'Lorem ipsum dolor sit amet',
			'created_user' => 1,
			'created' => '2015-05-18 01:36:42',
			'modified_user' => 1,
			'modified' => '2015-05-18 01:36:42'
		),
		array(
			'id' => 202,
			'block_id' => 202,
			'name' => 'キャビネット202',
			'key' => 'cabinet202',
			'is_auto_translated' => 1,
			'translation_engine' => 'Lorem ipsum dolor sit amet',
			'created_user' => 1,
			'created' => '2015-05-18 01:36:42',
			'modified_user' => 1,
			'modified' => '2015-05-18 01:36:42'
		),
		//array(
		//	'id' => 2,
		//	'block_id' => 2,
		//	'name' => 'Lorem ipsum dolor sit amet',
		//	'key' => 'Lorem ipsum dolor sit amet',
		//	'is_auto_translated' => 1,
		//	'translation_engine' => 'Lorem ipsum dolor sit amet',
		//	'created_user' => 2,
		//	'created' => '2015-05-18 01:36:42',
		//	'modified_user' => 2,
		//	'modified' => '2015-05-18 01:36:42'
		//),
		//array(
		//	'id' => 3,
		//	'block_id' => 3,
		//	'name' => 'Lorem ipsum dolor sit amet',
		//	'key' => 'Lorem ipsum dolor sit amet',
		//	'is_auto_translated' => 1,
		//	'translation_engine' => 'Lorem ipsum dolor sit amet',
		//	'created_user' => 3,
		//	'created' => '2015-05-18 01:36:42',
		//	'modified_user' => 3,
		//	'modified' => '2015-05-18 01:36:42'
		//),
		//array(
		//	'id' => 4,
		//	'block_id' => 4,
		//	'name' => 'Lorem ipsum dolor sit amet',
		//	'key' => 'Lorem ipsum dolor sit amet',
		//	'is_auto_translated' => 1,
		//	'translation_engine' => 'Lorem ipsum dolor sit amet',
		//	'created_user' => 4,
		//	'created' => '2015-05-18 01:36:42',
		//	'modified_user' => 4,
		//	'modified' => '2015-05-18 01:36:42'
		//),
		//array(
		//	'id' => 5,
		//	'block_id' => 5,
		//	'name' => 'Lorem ipsum dolor sit amet',
		//	'key' => 'Lorem ipsum dolor sit amet',
		//	'is_auto_translated' => 1,
		//	'translation_engine' => 'Lorem ipsum dolor sit amet',
		//	'created_user' => 5,
		//	'created' => '2015-05-18 01:36:42',
		//	'modified_user' => 5,
		//	'modified' => '2015-05-18 01:36:42'
		//),
		//array(
		//	'id' => 6,
		//	'block_id' => 6,
		//	'name' => 'Lorem ipsum dolor sit amet',
		//	'key' => 'Lorem ipsum dolor sit amet',
		//	'is_auto_translated' => 1,
		//	'translation_engine' => 'Lorem ipsum dolor sit amet',
		//	'created_user' => 6,
		//	'created' => '2015-05-18 01:36:42',
		//	'modified_user' => 6,
		//	'modified' => '2015-05-18 01:36:42'
		//),
		//array(
		//	'id' => 7,
		//	'block_id' => 7,
		//	'name' => 'Lorem ipsum dolor sit amet',
		//	'key' => 'Lorem ipsum dolor sit amet',
		//	'is_auto_translated' => 1,
		//	'translation_engine' => 'Lorem ipsum dolor sit amet',
		//	'created_user' => 7,
		//	'created' => '2015-05-18 01:36:42',
		//	'modified_user' => 7,
		//	'modified' => '2015-05-18 01:36:42'
		//),
		//array(
		//	'id' => 8,
		//	'block_id' => 8,
		//	'name' => 'Lorem ipsum dolor sit amet',
		//	'key' => 'Lorem ipsum dolor sit amet',
		//	'is_auto_translated' => 1,
		//	'translation_engine' => 'Lorem ipsum dolor sit amet',
		//	'created_user' => 8,
		//	'created' => '2015-05-18 01:36:42',
		//	'modified_user' => 8,
		//	'modified' => '2015-05-18 01:36:42'
		//),
		//array(
		//	'id' => 9,
		//	'block_id' => 9,
		//	'name' => 'Lorem ipsum dolor sit amet',
		//	'key' => 'Lorem ipsum dolor sit amet',
		//	'is_auto_translated' => 1,
		//	'translation_engine' => 'Lorem ipsum dolor sit amet',
		//	'created_user' => 9,
		//	'created' => '2015-05-18 01:36:42',
		//	'modified_user' => 9,
		//	'modified' => '2015-05-18 01:36:42'
		//),
		//array(
		//	'id' => 10,
		//	'block_id' => 10,
		//	'name' => 'Lorem ipsum dolor sit amet',
		//	'key' => 'Lorem ipsum dolor sit amet',
		//	'is_auto_translated' => 1,
		//	'translation_engine' => 'Lorem ipsum dolor sit amet',
		//	'created_user' => 10,
		//	'created' => '2015-05-18 01:36:42',
		//	'modified_user' => 10,
		//	'modified' => '2015-05-18 01:36:42'
		//),
	);

}
