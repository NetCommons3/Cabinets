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
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'cabinet_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'cabinet_file_tree_parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'status' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '公開状況  1:公開中、2:公開申請中、3:下書き中、4:差し戻し'),
		'is_active' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'is_latest' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'language_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'filename' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'タイトル', 'charset' => 'utf8'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '概要', 'charset' => 'utf8'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '作成者'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '作成日時'),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '更新者'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日時'),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'is_folder' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records id1〜8は予約
 *
 * @var array
 */
	public $records = array(
		// * ルーム管理者が書いたコンテンツ＆一度公開して、下書き中
		//   (id=1とid=2で区別できるものをセットする)
		array(
			'id' => '1',
			'cabinet_id' => '2',
			'status' => '1',
			'is_active' => true,
			'is_latest' => false,
			'language_id' => '2',
			'filename' => 'file1',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => '1',
			'created' => '2016-04-14 02:48:11',
			'modified_user' => '1',
			'modified' => '2016-04-14 02:48:11',
			'key' => 'content_key_1',
			'is_folder' => false,
		),
		array(
			'id' => '2',
			'cabinet_id' => '2',
			'status' => '3',
			'is_active' => false,
			'is_latest' => true,
			'language_id' => '2',
			'filename' => 'file2',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => '1',
			'created' => '2016-04-14 02:48:11',
			'modified_user' => '1',
			'modified' => '2016-04-14 02:48:11',
			'key' => 'content_key_1',
			'is_folder' => false,
		),
		// * 一般が書いたコンテンツ＆一度も公開していない（承認待ち）
		array(
			'id' => '3',
			'cabinet_id' => '2',
			'status' => '2',
			'is_active' => false,
			'is_latest' => true,
			'language_id' => '2',
			'filename' => 'file3',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => '4',
			'created' => '2016-04-14 02:48:11',
			'modified_user' => '1',
			'modified' => '2016-04-14 02:48:11',
			'key' => 'content_key_2',
			'is_folder' => false,
		),
		// * 一般が書いたコンテンツ＆公開して、一時保存
		//   (id=4とid=5で区別できるものをセットする)
		array(
			'id' => '4',
			'cabinet_id' => '2',
			'status' => '1',
			'is_active' => true,
			'is_latest' => false,
			'language_id' => '2',
			'filename' => 'file4',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => '4',
			'created' => '2016-04-14 02:48:11',
			'modified_user' => '1',
			'modified' => '2016-04-14 02:48:11',
			'key' => 'content_key_3',
			'is_folder' => false,
		),
		array(
			'id' => '5',
			'cabinet_id' => '2',
			'status' => '3',
			'is_active' => false,
			'is_latest' => true,
			'language_id' => '2',
			'filename' => 'file5',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => '4',
			'created' => '2016-04-14 02:48:11',
			'modified_user' => '1',
			'modified' => '2016-04-14 02:48:11',
			'key' => 'content_key_3',
			'is_folder' => false,
		),
		// * 編集者が書いたコンテンツ＆一度公開して、差し戻し
		//   (id=6とid=7で区別できるものをセットする)
		array(
			'id' => '6',
			'cabinet_id' => '2',
			'status' => '1',
			'is_active' => true,
			'is_latest' => false,
			'language_id' => '2',
			'filename' => 'file6',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => '3',
			'created' => '2016-04-14 02:48:11',
			'modified_user' => '1',
			'modified' => '2016-04-14 02:48:11',
			'key' => 'content_key_4',
			'is_folder' => false,
		),
		array(
			'id' => '7',
			'cabinet_id' => '2',
			'status' => '4',
			'is_active' => false,
			'is_latest' => true,
			'language_id' => '2',
			'filename' => 'file7',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => '3',
			'created' => '2016-04-14 02:48:11',
			'modified_user' => '1',
			'modified' => '2016-04-14 02:48:11',
			'key' => 'content_key_4',
			'is_folder' => false,
		),
		// * 編集長が書いたコンテンツ＆一度も公開していない（下書き中）
		array(
			'id' => '8',
			'cabinet_id' => '2',
			'status' => '3',
			'is_active' => false,
			'is_latest' => true,
			'language_id' => '2',
			'filename' => 'file8',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => '2',
			'created' => '2016-04-14 02:48:11',
			'modified_user' => '1',
			'modified' => '2016-04-14 02:48:11',
			'key' => 'content_key_5',
			'is_folder' => false,
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
		array(
			'id' => 11, // CabinetId2のRootFolder
			'cabinet_id' => 2,
			'status' => 1,
			'is_active' => 1,
			'is_latest' => 1,
			'language_id' => 2,
			'filename' => 'RootFolder2',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => 1,
			'created' => '2016-04-14 02:48:11',
			'modified_user' => 1,
			'modified' => '2016-04-14 02:48:11',
			'key' => 'content_key_11',
			'is_folder' => 1
		),
		// DownloadFolderTest Folder
		array(
			'id' => '12',
			'cabinet_id' => '2',
			'status' => '1',
			'is_active' => true,
			'is_latest' => true,
			'language_id' => '2',
			'filename' => 'Folder1',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => '1',
			'created' => '2016-04-14 02:48:11',
			'modified_user' => '1',
			'modified' => '2016-04-14 02:48:11',
			'key' => 'content_key_12',
			'is_folder' => true,
		),
		// DownloadFolderTest File
		// Folder1 id:13
		// +-Folder1-1 id:15
		//   +-File13 id:14
		array(
			'id' => '13',
			'cabinet_id' => '2',
			'status' => '1',
			'is_active' => true,
			'is_latest' => true,
			'language_id' => '2',
			'filename' => 'File13',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => '1',
			'created' => '2016-04-14 02:48:11',
			'modified_user' => '1',
			'modified' => '2016-04-14 02:48:11',
			'key' => 'content_key_13',
			'is_folder' => false,
		),
		array(
			'id' => '14',
			'cabinet_id' => '2',
			'status' => '1',
			'is_active' => true,
			'is_latest' => true,
			'language_id' => '2',
			'filename' => 'Folder1-1',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => '1',
			'created' => '2016-04-14 02:48:11',
			'modified_user' => '1',
			'modified' => '2016-04-14 02:48:11',
			'key' => 'content_key_14',
			'is_folder' => true,
		),
	);

}
