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
 *
 * @property Cabinet $Cabinet
 * @property CabinetFile $CabinetFile
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
		//'Cabinets.CabinetFrameSetting',
		'Blocks.Block',
		'Cabinets.CabinetFile',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'index,add,edit,delete' => 'block_editable',
			),
		),
		'Paginator',

	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Blocks.BlockForm',
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
		if (!$cabinets) {
			$this->view = 'Blocks.Blocks/not_found';
			return;
		}

		//foreach ($cabinets as &$cabinet){
		//	$uploadFile = ClassRegistry::init('Files.UploadFile');
		//	$uploadFile->find('all', [
		//		'field' => ['sum(size) AS total_size'],
		//		'conditions' => ['block_key' => $cabinet['Block']['key']],
		//
		//	])
		//	$result = $uploadFile->query('SELECT sum(size) AS total FROM upload_files WHERE block_key=' . $cabinet['Block']['key']);
		//	debug($result);
		//
		//}
		foreach ($cabinets as &$cabinet) {
			$rootFolder = $this->CabinetFile->getRootFolder($cabinet);
			$cabinet['Cabinet']['total_size'] = $this->CabinetFile->getTotalSizeByFolder(
				$rootFolder
			);
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

		if ($this->request->is('post')) {
			//登録処理
			if ($this->Cabinet->saveCabinet($this->data)) {
				$this->redirect(NetCommonsUrl::backToIndexUrl('default_setting_action'));
			}
			$this->NetCommons->handleValidationError($this->Cabinet->validationErrors);

		} else {
			//表示処理(初期データセット)
			$this->request->data = $this->Cabinet->createCabinet();
			//$this->request->data = Hash::merge($this->request->data, $this->CabinetFrameSetting->getCabinetFrameSetting(true));
			$this->request->data['Frame'] = Current::read('Frame');
		}
	}

/**
 * edit
 *
 * @return void
 */
	public function edit() {
		if ($this->request->is('put')) {
			//登録処理
			if ($this->Cabinet->saveCabinet($this->data)) {
				$this->redirect(NetCommonsUrl::backToIndexUrl('default_setting_action'));
			}
			$this->NetCommons->handleValidationError($this->Cabinet->validationErrors);

		} else {
			//表示処理(初期データセット)
			if (!$cabinet = $this->Cabinet->getCabinet()) {
				return $this->throwBadRequest();
			}
			$this->request->data = Hash::merge($this->request->data, $cabinet);
			//$this->request->data = Hash::merge($this->request->data, $this->CabinetFrameSetting->getCabinetFrameSetting(true));
			$this->request->data['Frame'] = Current::read('Frame');
		}
	}

/**
 * delete
 *
 * @return void
 */
	public function delete() {
		if ($this->request->is('delete')) {
			if ($this->Cabinet->deleteCabinet($this->data)) {
				return $this->redirect(NetCommonsUrl::backToIndexUrl('default_setting_action'));
			}
		}

		return $this->throwBadRequest();
	}

}
