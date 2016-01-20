<?php
/**
 * CabinetFilesController Test Case
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('CabinetFilesController', 'Cabinets.Controller');
App::uses('CabinetsAppControllerTestBase', 'Cabinets.Test/Case/Controller');

/**
 * Summary for CabinetFilesController Test Case
 */
class Controller_CabinetFiles_IndexTest extends CabinetsAppControllerTestBase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Configure::write('Config.language', 'ja');
		$this->cabinetFilesMock = $this->generate(
			'Cabinets.CabinetFiles',
			[
				'components' => [
					'Auth' => ['user'],
					'Session',
					'Security',
				]
			]
		);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		Configure::write('Config.language', null);
		CakeSession::write('Auth.User', null);
		parent::tearDown();
	}

/**
 * testIndex
 *
 * @return void
 */
	public function testIndex() {
		$view = $this->testAction(
			'/cabinets/cabinet_files/index/1',
			array(
				'method' => 'get',
				'return' => 'view',
			)
		);
		$this->assertInternalType('array', $this->vars['cabinetFiles']);
		// キャビネット名が表示される
		$this->assertRegExp('/<h1.*>キャビネット名<\/h1>/', $view);
	}

/**
 * testTag
 *
 * @return void
 */
	public function testTag() {
		$this->testAction(
			'/cabinets/cabinet_files/tag/1/id:1',
			array(
				'method' => 'get',
				//'return' => 'view',
			)
		);
		$this->assertInternalType('array', $this->vars['cabinetFiles']);
	}

/**
 * testYearMonth
 *
 * @return void
 */
	public function testYearMonth() {
		$this->testAction(
			'/cabinets/cabinet_files/year_month/1/year_month:2014-02',
			array(
				'method' => 'get',
				//'return' => 'view',
			)
		);
		$this->assertInternalType('array', $this->vars['cabinetFiles']);
	}

/**
 * フレームがあってブロックがないときのテスト
 *
 * @return void
 */
	public function testNoBlock() {
		$result = $this->testAction(
			'/cabinets/cabinet_files/index/201',
			array(
				'method' => 'get',
				'return' => 'view',
			)
		);
		$this->assertEquals('', $result);
	}

/**
 * カテゴリのファイル一覧
 *
 * @return void
 */
	public function testCategory() {
		$return = $this->testAction(
			'/cabinets/cabinet_files/index/1/category_id:1',
			array(
				'method' => 'get',
				'return' => 'view',
			)
		);
		$this->assertRegExp('/<h1.*>カテゴリ:category_1<\/h1>/', $return);
	}
}
