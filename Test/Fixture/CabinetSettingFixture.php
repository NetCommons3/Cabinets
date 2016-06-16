<?php
/**
 * CabinetSettingFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Your Name <yourname@domain.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
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
		'id' => array(
			'type' => 'integer',
			'null' => false,
			'default' => null,
			'unsigned' => false,
			'key' => 'primary',
			'comment' => 'ID | | | '
		),
		'cabinet_key' => array(
			'type' => 'string',
			'null' => false,
			'default' => null,
			'collate' => 'utf8_general_ci',
			'comment' => 'Cabinet key | CABINETキー | Hash値 | ',
			'charset' => 'utf8'
		),
		'use_workflow' => array(
			'type' => 'boolean',
			'null' => false,
			'default' => '1',
			'comment' => 'Use workflow, 0:Unused 1:Use | コンテンツの承認機能 0:使わない 1:使う | | '
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
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'cabinet_key' => 'content_block_1',
			'use_workflow' => true,
			'created_user' => 1,
			'created' => '2016-04-14 02:49:54',
			'modified_user' => 1,
			'modified' => '2016-04-14 02:49:54'
		),
	);

}
