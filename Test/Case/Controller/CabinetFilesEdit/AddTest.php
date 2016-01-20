<?php
/**
 * Created by PhpStorm.
 * User: ryuji
 * Date: 15/05/18
 * Time: 9:56
 */

App::uses('CabinetFilesEditController', 'Cabinets.Controller');
App::uses('CabinetsAppControllerTestBase', 'Cabinets.Test/Case/Controller');

/**
 * CabinetsController Test Case
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Controller
 */
class CabinetsFilesEdit_AddTest extends CabinetsAppControllerTestBase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->cabinetFilesEditMock = $this->generate(
			'Cabinets.CabinetFilesEdit',
			[
				'methods' => [
					'handleValidationError',
				],
				'components' => [
					'Auth' => ['user'],
					'Session',
					'Security',
					'NetCommons.NetCommonsWorkflow'
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
 * test add action validate fail
 *
 * @return void
 */
	public function testAddPostValidateFail() {
		$this->cabinetFilesEditMock->NetCommonsWorkflow->expects($this->once())
			->method('parseStatus')
			->will($this->returnValue(1));

		RolesControllerTest::login($this);

		// validate error発生でhandleValidationError()が呼ばれる。
		$this->cabinetFilesEditMock->expects($this->once())
			->method('handleValidationError')
			->with($this->isType('array'));
		$this->testAction(
			'/cabinets/cabinet_files_edit/add/1',
			array(
				'method' => 'post',
			)
		);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * test add action post success
 *
 * @return void
 */
	public function testAddPostSuccess() {
		$this->cabinetFilesEditMock->NetCommonsWorkflow->expects($this->once())
			->method('parseStatus')
			->will($this->returnValue(1));

		RolesControllerTest::login($this);

		$data = array();
		$data['CabinetFile']['category_id'] = 0;
		$data['CabinetFile']['title'] = 'New File';
		$data['CabinetFile']['body1'] = 'body1';
		$data['CabinetFile']['key'] = '';
		$data['CabinetFile']['status'] = 1;
		$data['CabinetFile']['key'] = 0;
		$data['CabinetFile']['language_id'] = 1;
		$data['CabinetFile']['publish_start'] = '2015-01-01 00:00:00';
		$data['CabinetFile']['block_id'] = 5;
		$data['CabinetFile']['cabinet_key'] = 'cabinet1';

		$data['Comment']['comment'] = '';

		$this->testAction(
			'/cabinets/cabinet_files_edit/add/1',
			array(
				'method' => 'post',
				'data' => $data,
			)
		);
		$this->assertRegExp('#cabinets/cabinet_files/view#', $this->headers['Location']);
		AuthGeneralControllerTest::logout($this);
	}

}

