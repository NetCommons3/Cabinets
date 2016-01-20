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
class Controller_CabinetFiles_ViewTest extends CabinetsAppControllerTestBase {

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
 * test view.編集リンクの表示テスト
 *
 * @param string $role ロール
 * @param bool $viewEditLink 編集リンクが表示されるか
 * @dataProvider editLinkDataProvider
 * @return void
 */
	public function testEditLink($role, $viewEditLink) {
		RolesControllerTest::login($this, $role);
		$view = $this->testAction(
			'/cabinets/cabinet_files/view/1/key:6',
			array(
				'method' => 'get',
				'return' => 'view',
			)
		);
		$this->assertInternalType('array', $this->vars['cabinetFile']);
		if ($viewEditLink) {
			$this->assertTextContains('nc-cabinet-edit-link', $view);
		} else {
			$this->assertTextNotContains('nc-cabinet-edit-link', $view);
		}
		AuthGeneralControllerTest::logout($this);
	}

/**
 * testEditLink用dataProvider
 *
 * @return array
 */
	public function editLinkDataProvider() {
		$data = [
			['chief_editor', true],
			['editor', true],
			['general_user', true],
			['visitor', false],
		];
		return $data;
	}

/**
 * test view action まだ公開されてないファイルはNotFoundException
 *
 * @return void
 */
	public function testViewNotFound() {
		$this->setExpectedException('NotFoundException');
		// key:4はまだ公開されてない
		$this->testAction(
			'/cabinets/cabinet_files/view/1/key:4',
			array(
				'method' => 'get',
				//'return' => 'view',
			)
		);
	}

/**
 * test view . タグの表示
 *
 * @return void
 */
	public function testViewWithTag() {
		$view = $this->testAction(
			'/cabinets/cabinet_files/view/1/key:1',
			array(
				'method' => 'get',
				'return' => 'view',
			)
		);
		$this->assertTextContains('Tag1', $view);
	}

/**
 * test view action content comment post fail -> bad request
 *
 * @return void
 */
	public function testViewContentCommentPostFailed() {
		$cabinetFilesMock = $this->generate(
			'Cabinets.CabinetFiles',
			[
				'components' => [
					'Auth' => ['user'],
					'Session',
					'Security',
					'ContentComments.ContentComments' => ['comment']
				],
			]
		);
		$cabinetFilesMock->ContentComments->expects($this->once())
			->method('comment')
			->will($this->returnValue(false));

		$this->setExpectedException('BadRequestException');

		$this->testAction(
			'/cabinets/cabinet_files/view/1/key:1',
			array(
				'method' => 'post',
				//'return' => 'view',
			)
		);
	}
}
