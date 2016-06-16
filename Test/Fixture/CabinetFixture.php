<?php
/**
 * CabinetFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Your Name <yourname@domain.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
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
		'id' => array(
			'type' => 'integer',
			'null' => false,
			'default' => null,
			'unsigned' => false,
			'key' => 'primary',
			'comment' => 'ID | | | '
		),
		'block_id' => array(
			'type' => 'integer',
			'null' => false,
			'default' => null,
			'unsigned' => false
		),
		'name' => array(
			'type' => 'string',
			'null' => false,
			'default' => null,
			'collate' => 'utf8_general_ci',
			'comment' => 'CABINET name | CABINET名称 | | ',
			'charset' => 'utf8'
		),
		'key' => array(
			'type' => 'string',
			'null' => false,
			'default' => null,
			'collate' => 'utf8_general_ci',
			'comment' => 'cabinet key | CABINETキー | Hash値 | ',
			'charset' => 'utf8'
		),
		'created_user' => array(
			'type' => 'integer',
			'null' => true,
			'default' => '0',
			'unsigned' => false,
			'comment' => 'created user | 作成者 | users.id | '
		),
		'created' => array(
			'type' => 'datetime',
			'null' => true,
			'default' => null,
			'comment' => 'created datetime | 作成日時 | | '
		),
		'modified_user' => array(
			'type' => 'integer',
			'null' => true,
			'default' => '0',
			'unsigned' => false,
			'comment' => 'modified user | 更新者 | users.id | '
		),
		'modified' => array(
			'type' => 'datetime',
			'null' => true,
			'default' => null,
			'comment' => 'modified datetime | 更新日時 | | '
		),
		'total_size' => array(
			'type' => 'float',
			'null' => true,
			'default' => null,
			'unsigned' => false
		),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array(
			'charset' => 'utf8',
			'collate' => 'utf8_general_ci',
			'engine' => 'InnoDB'
		)
	);

/**
 * Records id1-2は予約
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 2,
			'block_id' => '2',
			'name' => 'Cabinet2',
			'key' => 'content_block_1',
			'created_user' => 1,
			'created' => '2016-04-14 02:49:44',
			'modified_user' => 1,
			'modified' => '2016-04-14 02:49:44',
			'total_size' => '1'
		),
		array(
			'id' => 4,
			'block_id' => '4',
			'name' => 'Cabinet4',
			'key' => 'content_block_2',
			'created_user' => 2,
			'created' => '2016-04-14 02:49:44',
			'modified_user' => 2,
			'modified' => '2016-04-14 02:49:44',
			'total_size' => '1'
		),
		// この上までテストテンプレの予約
		array(
			'id' => 3,
			'block_id' => 3,
			'name' => 'Cabinet3',
			'key' => 'cabinet_3',
			'created_user' => 1,
			'created' => '2016-04-14 02:49:44',
			'modified_user' => 1,
			'modified' => '2016-04-14 02:49:44',
			'total_size' => 1
		),
	);

}
