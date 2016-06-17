<?php
/**
 * UploadFilesContentForCabinetsFixture
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('UploadFilesContentFixture', 'Files.Test/Fixture');

/**
 * UploadFilesContentForCabinetsFixture
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Fixture
 */
class UploadFilesContentForCabinetsFixture extends UploadFilesContentFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'UploadFilesContent';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'upload_files_contents';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array( // cabinets
			'id' => 7,
			'plugin_key' => 'cabinets',
			'content_id' => 2,
			'upload_file_id' => 11,
		),
		array( // cabinets
			'id' => 8,
			'plugin_key' => 'cabinets',
			'content_id' => 1,
			'upload_file_id' => 12,
		),
		array( // cabinets
			'id' => 9,
			'plugin_key' => 'cabinets',
			'content_id' => 4,
			'upload_file_id' => 13,
		),
		array( // cabinets
			'id' => 10,
			'plugin_key' => 'cabinets',
			'content_id' => 5,
			'upload_file_id' => 13,
		),
		array( // cabinets
			'id' => 11,
			'plugin_key' => 'cabinets',
			'content_id' => 6,
			'upload_file_id' => 14,
		),
		array( // cabinets
			'id' => 12,
			'plugin_key' => 'cabinets',
			'content_id' => 3,
			'upload_file_id' => 15,
		),
		array( // cabinets
			'id' => 13,
			'plugin_key' => 'cabinets',
			'content_id' => 7,
			'upload_file_id' => 14,
		),
		array( // cabinets
			'id' => 14,
			'plugin_key' => 'cabinets',
			'content_id' => 8,
			'upload_file_id' => 16,
		),
	);

}
