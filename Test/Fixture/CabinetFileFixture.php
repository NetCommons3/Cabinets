<?php
/**
 * CabinetFileFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Your Name <yourname@domain.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for CabinetFileFixture
 */
class CabinetFileFixture extends CakeTestFixture {

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
			'comment' => 'ID |  |  | '
		),
		'cabinet_id' => array(
			'type' => 'integer',
			'null' => false,
			'default' => null,
			'unsigned' => false
		),
		'status' => array(
			'type' => 'integer',
			'null' => false,
			'default' => null,
			'length' => 4,
			'unsigned' => false,
			'comment' => 'public status, 1: public, 2: public pending, 3: draft during 4: remand | 公開状況  1:公開中、2:公開申請中、3:下書き中、4:差し戻し |  | '
		),
		'is_active' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'is_latest' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'language_id' => array(
			'type' => 'integer',
			'null' => true,
			'default' => null,
			'unsigned' => false
		),
		'filename' => array(
			'type' => 'string',
			'null' => true,
			'default' => null,
			'collate' => 'utf8_general_ci',
			'comment' => 'title | タイトル |  | ',
			'charset' => 'utf8'
		),
		'description' => array(
			'type' => 'text',
			'null' => true,
			'default' => null,
			'collate' => 'utf8_general_ci',
			'comment' => 'file body1 | 本文1 |  | ',
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
			'comment' => 'created datetime | 作成日時 |  | '
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
			'comment' => 'modified datetime | 更新日時 |  | '
		),
		'key' => array(
			'type' => 'string',
			'null' => false,
			'default' => null,
			'collate' => 'utf8_general_ci',
			'charset' => 'utf8'
		),
		'is_folder' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
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
 * Records id1〜8は予約
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'cabinet_id' => 1,
			'status' => 1,
			'is_active' => 1,
			'is_latest' => 1,
			'language_id' => 1,
			'filename' => 'Lorem ipsum dolor sit amet',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => 1,
			'created' => '2016-04-14 02:48:11',
			'modified_user' => 1,
			'modified' => '2016-04-14 02:48:11',
			'key' => 'Lorem ipsum dolor sit amet',
			'is_folder' => 1
		),
		array(
			'id' => 10, // CabinetId3のRootFolder
			'cabinet_id' => 3,
			'status' => 1,
			'is_active' => 1,
			'is_latest' => 1,
			'language_id' => 2,
			'filename' => 'FileName',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => 1,
			'created' => '2016-04-14 02:48:11',
			'modified_user' => 1,
			'modified' => '2016-04-14 02:48:11',
			'key' => 'content_key_10',
			'is_folder' => 1
		),
	);

}
