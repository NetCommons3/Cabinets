<?php
/**
 * CabinetFilesController::download()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * CabinetFilesController::view()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Controller\CabinetFilesController
 */
class CabinetFilesControllerDownloadTest extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet',
		'plugin.cabinets.cabinet_file',
		'plugin.cabinets.cabinet_file_tree',
		'plugin.cabinets.block_setting_for_cabinet',
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
		$contentKey = 'content_key_1';

		$data = array(
			'action' => 'download',
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
		$this->generate(
			'Caabinets.CabinetFiles',
			[
				'components' => [
					'Download'
				]
			]
		);

		$this->controller->Components->Download
			->expects($this->once())
			->method('doDownload')
			->with(
				$this->equalTo(1),
				$this->equalTo(
					[
						'field' => 'file',
						'download' => true,
						'name' => 'file1'
					]
				)
			);

		$urlOptions = $this->__data();

		//テスト実施
		$this->_testGetAction($urlOptions, array('method' => 'assertEmpty'), null, 'result');
	}
}
