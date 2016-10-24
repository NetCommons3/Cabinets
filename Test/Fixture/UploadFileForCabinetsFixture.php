<?php
/**
 * UploadFileForCabinetsFixture
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('UploadFileFixture', 'Files.Test/Fixture');

/**
 * UploadFileForCabinetsFixture
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Fixture
 */
class UploadFileForCabinetsFixture extends UploadFileFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'UploadFile';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'upload_files';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array( // cabinets
			'id' => 11,
			'plugin_key' => 'cabinets',
			'content_key' => 'content_key_1',
			'field_name' => 'file',
			'original_name' => 'cabinets1.mp4',
			'path' => 'files/upload_file/real_file_name/1/',
			'real_file_name' => 'cabinets1.mp4',
			'extension' => 'mp4',
			'mimetype' => 'cabinets/mp4',
			'size' => 4544587,
			'download_count' => 11,
			'total_download_count' => 11,
			'room_id' => '2',
			'block_key' => 'block_1',
		),
		array( // cabinets
			'id' => 12,
			'plugin_key' => 'cabinets',
			'content_key' => 'content_key_1',
			'field_name' => 'file',
			'original_name' => 'cabinets2.MOV',
			'path' => 'files/upload_file/real_file_name/1/',
			'real_file_name' => 'cabinets2.MOV',
			'extension' => 'MOV',
			'mimetype' => 'cabinets/quicktime',
			'size' => 4544587,
			'download_count' => 12,
			'total_download_count' => 12,
			'room_id' => '2',
			'block_key' => 'block_1',
		),
		array( // cabinets
			'id' => 13,
			'plugin_key' => 'cabinets',
			'content_key' => 'content_key_3',
			'field_name' => 'file',
			'original_name' => 'cabinets2.MOV',
			'path' => 'files/upload_file/real_file_name/1/',
			'real_file_name' => 'cabinets2.MOV',
			'extension' => 'MOV',
			'mimetype' => 'cabinets/quicktime',
			'size' => 4544587,
			'download_count' => 12,
			'total_download_count' => 12,
			'room_id' => '2',
			'block_key' => 'block_1',
		),
		array( // cabinets
			'id' => 14,
			'plugin_key' => 'cabinets',
			'content_key' => 'content_key_4',
			'field_name' => 'file',
			'original_name' => 'cabinets2.MOV',
			'path' => 'files/upload_file/real_file_name/1/',
			'real_file_name' => 'cabinets2.MOV',
			'extension' => 'MOV',
			'mimetype' => 'cabinets/quicktime',
			'size' => 4544587,
			'download_count' => 12,
			'total_download_count' => 12,
			'room_id' => '2',
			'block_key' => 'block_1',
		),
		array( // cabinets
			'id' => 15,
			'plugin_key' => 'cabinets',
			'content_key' => 'content_key_2',
			'field_name' => 'file',
			'original_name' => 'cabinets2.MOV',
			'path' => 'files/upload_file/real_file_name/1/',
			'real_file_name' => 'cabinets2.MOV',
			'extension' => 'MOV',
			'mimetype' => 'cabinets/quicktime',
			'size' => 4544587,
			'download_count' => 12,
			'total_download_count' => 12,
			'room_id' => '2',
			'block_key' => 'block_1',
		),
		array( // cabinets
			'id' => 16,
			'plugin_key' => 'cabinets',
			'content_key' => 'content_key_5',
			'field_name' => 'file',
			'original_name' => 'cabinets2.MOV',
			'path' => 'files/upload_file/real_file_name/1/',
			'real_file_name' => 'cabinets2.MOV',
			'extension' => 'MOV',
			'mimetype' => 'cabinets/quicktime',
			'size' => 4544587,
			'download_count' => 12,
			'total_download_count' => 12,
			'room_id' => '2',
			'block_key' => 'block_1',
		),
		array( // DownloadFolderTest
			'id' => 17,
			'plugin_key' => 'cabinets',
			'content_key' => 'content_key_13',
			'field_name' => 'file',
			'original_name' => 'logo.gif',
			'path' => 'files/upload_file/real_file_name/1/',
			'real_file_name' => 'logo.gif',
			'extension' => 'gif',
			'mimetype' => 'image/gif',
			'size' => 5000,
			'download_count' => 12,
			'total_download_count' => 12,
			'room_id' => '2',
			'block_key' => 'block_1',
		),
	);

}
