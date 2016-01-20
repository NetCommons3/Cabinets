<?php
/**
 * CabinetBlocks Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('CabinetsAppController', 'Cabinets.Controller');

/**
 * CabinetBlocks Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Cabinets\Controller
 */
class CabinetBlocksController extends CabinetsAppController {

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
		'Blocks.Block',
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
				'index,add,edit,delete' => 'block_editable',
			),
		),
		'Paginator',
		'Categories.CategoryEdit',

	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Blocks.BlockForm',
		//'Blocks.Block',
		'Likes.Like',
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		//CategoryEditComponentの削除
		if ($this->params['action'] === 'index') {
			$this->Components->unload('Categories.CategoryEdit');
		}
	}

/**
 * index
 *
 * @return void
 */
	public function index() {
		$this->Paginator->settings = array(
			'Cabinet' => array(
				'order' => array('Cabinet.id' => 'desc'),
				'conditions' => $this->Cabinet->getBlockConditions(),
			)
		);

		$cabinets = $this->Paginator->paginate('Cabinet');
		if (! $cabinets) {
			$this->view = 'Blocks.Blocks/not_found';
			return;
		}
		$this->set('cabinets', $cabinets);
		$this->request->data['Frame'] = Current::read('Frame');
	}

/**
 * add
 *
 * @return void
 */
	public function add() {
		$this->view = 'edit';

		if ($this->request->isPost()) {
			//登録処理
			if ($this->Cabinet->saveCabinet($this->data)) {
				$this->redirect(NetCommonsUrl::backToIndexUrl('default_setting_action'));
			}
			$this->NetCommons->handleValidationError($this->Cabinet->validationErrors);

		} else {
			//表示処理(初期データセット)
			$this->request->data = $this->Cabinet->createCabinet();
			$this->request->data = Hash::merge($this->request->data, $this->CabinetFrameSetting->getCabinetFrameSetting(true));
			$this->request->data['Frame'] = Current::read('Frame');
		}
	}

/**
 * edit
 *
 * @return void
 */
	public function edit() {
		if ($this->request->isPut()) {
			//登録処理
			if ($this->Cabinet->saveCabinet($this->data)) {
				$this->redirect(NetCommonsUrl::backToIndexUrl('default_setting_action'));
			}
			$this->NetCommons->handleValidationError($this->Cabinet->validationErrors);

		} else {
			//表示処理(初期データセット)
			if (! $cabinet = $this->Cabinet->getCabinet()) {
				$this->setAction('throwBadRequest');
				return false;
			}
			$this->request->data = Hash::merge($this->request->data, $cabinet);
			$this->request->data = Hash::merge($this->request->data, $this->CabinetFrameSetting->getCabinetFrameSetting(true));
			$this->request->data['Frame'] = Current::read('Frame');
		}
	}

/**
 * delete
 *
 * @return void
 */
	public function delete() {
		if ($this->request->isDelete()) {
			if ($this->Cabinet->deleteCabinet($this->data)) {
				$this->redirect(NetCommonsUrl::backToIndexUrl('default_setting_action'));
			}
		}

		$this->setAction('throwBadRequest');
	}
}
