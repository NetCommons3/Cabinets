<?php
/**
 * Config/routes.phpのテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsRoutesTestCase', 'NetCommons.TestSuite');

/**
 * Config/routes.phpのテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Routing\Route\SlugRoute
 */
class RoutesTest extends NetCommonsRoutesTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'cabinets';

/**
 * DataProvider
 *
 * ### 戻り値
 * - url URL
 * - expected 期待値
 *
 * @return array テストデータ
 */
	public function dataProvider() {
		return array(
			array(
				'url' => '/cabinets/cabinet_files/index/1/content_key',
				'expected' => array(
					'plugin' => 'cabinets', 'controller' => 'cabinet_files', 'action' => 'index',
					'block_id' => '1', 'key' => 'content_key',
				)
			),
			array(
				'url' => '/cabinets/cabinet_files/folder_detail/1/content_key',
				'expected' => array(
					'plugin' => 'cabinets', 'controller' => 'cabinet_files', 'action' => 'folder_detail',
					'block_id' => '1', 'key' => 'content_key',
				)
			),
			array(
				'url' => '/cabinets/cabinet_files/view/1/content_key',
				'expected' => array(
					'plugin' => 'cabinets', 'controller' => 'cabinet_files', 'action' => 'view',
					'block_id' => '1', 'key' => 'content_key',
				)
			),
			array(
				'url' => '/cabinets/cabinet_files/download/1/content_key',
				'expected' => array(
					'plugin' => 'cabinets', 'controller' => 'cabinet_files', 'action' => 'download',
					'block_id' => '1', 'key' => 'content_key',
				)
			),
			array(
				'url' => '/cabinets/cabinet_files/download_folder/1/content_key',
				'expected' => array(
					'plugin' => 'cabinets', 'controller' => 'cabinet_files', 'action' => 'download_folder',
					'block_id' => '1', 'key' => 'content_key',
				)
			),
			array(
				'url' => '/cabinets/cabinet_files_edit/edit/1/content_key',
				'expected' => array(
					'plugin' => 'cabinets', 'controller' => 'cabinet_files_edit', 'action' => 'edit',
					'block_id' => '1', 'key' => 'content_key'
				)
			),
			array(
				'url' => '/cabinets/cabinet_files_edit/edit_folder/1/content_key',
				'expected' => array(
					'plugin' => 'cabinets', 'controller' => 'cabinet_files_edit', 'action' => 'edit_folder',
					'block_id' => '1', 'key' => 'content_key'
				)
			),
		);
	}

}
