<?php
/**
 * CabinetFilesController::download_folder()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * CabinetFilesController::download_folder()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Controller\CabinetFilesController
 */
class CabinetFilesControllerDownloadFolderTest extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet',
		'plugin.cabinets.cabinet_file',
		'plugin.cabinets.cabinet_file_tree',
		'plugin.cabinets.cabinet_setting',
		'plugin.workflow.workflow_comment',
		'plugin.authorization_keys.authorization_keys',
		'plugin.site_manager.site_setting',
		'plugin.cabinets.upload_file_for_cabinets',
		'plugin.cabinets.upload_files_content_for_cabinets',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'cabinets';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'cabinet_files';

/**
 * テストDataの取得
 *
 * @return array
 */
	private function __data() {
		$frameId = '6';
		$blockId = '2';
		$contentKey = 'content_key_12';

		$data = array(
			'action' => 'download_folder',
			'frame_id' => $frameId,
			'block_id' => $blockId,
			'key' => $contentKey,
			'file'
		);

		return $data;
	}

/**
 * download()アクションのGetリクエストテスト
 *
 * @return void
 */
	public function testDownloadGet() {
		$folderName = 'Folder1';
		$controller = $this->generate(
			'Cabinets.CabinetFiles',
			[
				'methods' => [
					'_getZipDownloader'
				],
				//'components' => [
				//	'Download'
				//]
			]
		);
		$zipDownloaderMock = $this->getMock('ZipDownloader', ['download']);
		$zipDownloaderMock->expects($this->once())
			->method('download')
			->with(
				$this->equalTo($folderName . '.zip')
			);
		$controller->expects($this->once())
			->method('_getZipDownloader')
			->will($this->returnValue($zipDownloaderMock));

		//UploadFileの準備
		$this->UploadFile = $this->getMockForModel('Files.UploadFile', ['getRealFilePath']);

		$this->UploadFile->expects($this->any())
			->method('getRealFilePath')
			->will($this->returnValue(APP . 'Plugin/Cabinets/Test/Fixture/logo.gif'));

		$urlOptions = $this->__data();

		//テスト実施
		$this->_testGetAction($urlOptions, array('method' => 'assertEmpty'), null, 'result');
	}
}