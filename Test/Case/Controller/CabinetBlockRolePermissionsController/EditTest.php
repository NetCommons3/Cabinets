<?php
/**
 * CabinetBlockRolePermissionsController::edit()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('BlockRolePermissionsControllerEditTest', 'Blocks.TestSuite');

/**
 * CabinetBlockRolePermissionsController::edit()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Controller\CabinetBlockRolePermissionsController
 */
class CabinetBlockRolePermissionsControllerEditTest extends BlockRolePermissionsControllerEditTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet',
		'plugin.cabinets.cabinet_file',
		'plugin.cabinets.cabinet_setting',
		'plugin.workflow.workflow_comment',
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
	protected $_controller = 'cabinet_block_role_permissions';

/**
 * 権限設定で使用するFieldsの取得
 *
 * @return array
 */
	private function __approvalFields() {
		$data = array(
			'CabinetSetting' => array(
				'use_workflow',
				//'use_comment_approval',
				//'approval_type',
			)
		);

		return $data;
	}

/**
 * テストDataの取得
 *
 * @return array
 */
	private function __data() {
		$data = array(
			'CabinetSetting' => array(
				'id' => 2,
				'cabinet_key' => 'cabinet_key_2',
				'use_workflow' => true,
				//'use_comment_approval' => true,
				//'approval_type' => true,
			)
		);

		return $data;
	}

/**
 * edit()アクションDataProvider
 *
 * ### 戻り値
 *  - approvalFields コンテンツ承認の利用有無のフィールド
 *  - exception Exception
 *  - return testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGet() {
		return array(
			array('approvalFields' => $this->__approvalFields()),
		);
	}

/**
 * edit()アクションDataProvider
 *
 * ### 戻り値
 *  - data POSTデータ
 *  - exception Exception
 *  - return testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditPost() {
		return array(
			array('data' => $this->__data())
		);
	}

/**
 * editアクションのGETテスト(Exceptionエラー)
 *
 * @param array $approvalFields コンテンツ承認の利用有無のフィールド
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderEditGet
 * @return void
 */
	public function testEditGetExceptionError($approvalFields, $exception = null, $return = 'view') {
		$this->_mockForReturnFalse('Cabinets.Cabinet', 'getCabinet');

		$exception = 'BadRequestException';
		$this->testEditGet($approvalFields, $exception, $return);
	}

/**
 * test Post でのsaveCabinetSetting 失敗
 *
 * @param array $data saveデータ
 * @return void
 * @dataProvider dataProviderEditPost
 */
	public function testSaveCabinetSettingFail($data) {
		$this->_mockForReturnFalse('Cabinets.CabinetSetting', 'saveCabinetSetting');

		$result = $this->testEditPost($data, false, 'view');
		$approvalFields = $this->__approvalFields();
		$this->_assertEditGetPermission($approvalFields, $result);
	}
}
