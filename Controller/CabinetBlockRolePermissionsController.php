<?php
/**
 * BlockRolePermissions Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('CabinetsAppController', 'Cabinets.Controller');

/**
 * BlockRolePermissions Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Cabinets\Controller
 */
class CabinetBlockRolePermissionsController extends CabinetsAppController {

/**
 * layout
 *
 * @var array
 */
	public $layout = 'NetCommons.setting';

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Cabinets.Cabinet',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		//'Blocks.BlockTabs' => array(
		//	'mainTabs' => array(
		//		'block_index' => array('url' => array('controller' => 'cabinet_blocks')),
		//		//'frame_settings' => array('url' => array('controller' => 'cabinet_frame_settings')),
		//	),
		//	'blockTabs' => array(
		//		'block_settings' => array('url' => array('controller' => 'cabinet_blocks')),
		//		'role_permissions' => array('url' => array('controller' => 'cabinet_block_role_permissions')),
		//	),
		//),
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'edit' => 'block_permission_editable',
			),
		),
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Blocks.BlockRolePermissionForm',
		'Blocks.BlockTabs' => array(
			'mainTabs' => array(
				'block_index' => array('url' => array('controller' => 'cabinet_blocks')),
				//'frame_settings' => array('url' => array('controller' => 'cabinet_frame_settings')),
			),
			'blockTabs' => array(
				'block_settings' => array('url' => array('controller' => 'cabinet_blocks')),
				'mail_settings',
				'role_permissions' => array('url' => array('controller' => 'cabinet_block_role_permissions')),
			),
		),
	);

/**
 * edit
 *
 * @return void
 */
	public function edit() {
		if (!$cabinet = $this->Cabinet->getCabinet()) {
			return $this->setAction('throwBadRequest');
		}

		$permissions = $this->Workflow->getBlockRolePermissions(
			array(
				'content_creatable',
				'content_publishable',
				'content_comment_creatable',
				'content_comment_publishable'
			)
		);
		$this->set('roles', $permissions['Roles']);

		if ($this->request->is('post')) {
			if ($this->CabinetSetting->saveCabinetSetting($this->request->data)) {
				return $this->redirect(NetCommonsUrl::backToIndexUrl('default_setting_action'));
			}
			$this->NetCommons->handleValidationError($this->CabinetSetting->validationErrors);
			$this->request->data['BlockRolePermission'] = Hash::merge(
				$permissions['BlockRolePermissions'],
				$this->request->data['BlockRolePermission']
			);

		} else {
			$this->request->data['CabinetSetting'] = $cabinet['CabinetSetting'];
			$this->request->data['Block'] = $cabinet['Block'];
			$this->request->data['BlockRolePermission'] = $permissions['BlockRolePermissions'];
			$this->request->data['Frame'] = Current::read('Frame');
		}
	}
}
