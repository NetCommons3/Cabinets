<?php
/**
 * CabinetFrameSettings Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('CabinetsAppController', 'Cabinets.Controller');

/**
 * CabinetFrameSettings Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Cabinets\Controller
 */
class CabinetFrameSettingsController extends CabinetsAppController {

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
		'Cabinets.CabinetFrameSetting',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'Blocks.BlockTabs' => array(
			'mainTabs' => array(
				'block_index' => array('url' => array('controller' => 'cabinet_blocks')),
				'frame_settings' => array('url' => array('controller' => 'cabinet_frame_settings')),
			),
			'blockTabs' => array(
				'block_settings' => array('url' => array('controller' => 'cabinet_blocks')),
				'role_permissions' => array('url' => array('controller' => 'cabinet_block_role_permissions')),
			),
		),
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'edit' => 'page_editable',
			),
		),
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.DisplayNumber',
	);

/**
 * edit
 *
 * @return void
 */
	public function edit() {
		if ($this->request->isPut() || $this->request->isPost()) {
			if ($this->CabinetFrameSetting->saveCabinetFrameSetting($this->data)) {
				$this->redirect(NetCommonsUrl::backToPageUrl());
				return;
			}
			$this->NetCommons->handleValidationError($this->CabinetFrameSetting->validationErrors);
		} else {
			$this->request->data = $this->CabinetFrameSetting->getCabinetFrameSetting(true);
			$this->request->data['Frame'] = Current::read('Frame');
		}
	}
}
