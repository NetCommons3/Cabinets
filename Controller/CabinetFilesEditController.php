<?php
/**
 * CabinetFilesEdit
 */
App::uses('CabinetsAppController', 'Cabinets.Controller');

/**
 * CabinetFilesEdit Controller
 *
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 * @property NetCommonsWorkflow $NetCommonsWorkflow
 * @property PaginatorComponent $Paginator
 * @property CabinetFile $CabinetFile
 * @property CabinetCategory $CabinetCategory
 * @property NetCommonsComponent $NetCommons
 */
class CabinetFilesEditController extends CabinetsAppController {

/**
 * @var array use models
 */
	public $uses = array(
		'Cabinets.CabinetFile',
		'Cabinets.CabinetFileTree',
		'Workflow.WorkflowComment',
	);

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'add,edit,delete' => 'content_creatable',
				'add_folder,edit_folder' => 'content_editable', // フォルダの作成・編集は編集権限以上
				'unzip' => 'content_publishable'
			),
		),
		'Workflow.Workflow',

		'Categories.Categories',
		//'Cabinets.CabinetFilePermission',
		'NetCommons.NetCommonsTime',
		'Files.FileUpload',
		'Files.Download',
	);

/**
 * @var array helpers
 */
	public $helpers = array(
		//'NetCommons.Token',
		'NetCommons.BackTo',
		'NetCommons.NetCommonsForm',
		'Workflow.Workflow',
		'NetCommons.NetCommonsTime',
		//'Likes.Like',
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$blockId = Current::read('Block.id');
		$this->_cabinet = $this->Cabinet->findByBlockId($blockId);
		$this->set('cabinet', $this->_cabinet);
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		//レイアウトの設定
		$this->viewClass = 'View';
		$this->layout = 'NetCommons.modal';

		$this->set('isEdit', false);

		$cabinetFile = $this->CabinetFile->getNew();
		$this->set('cabinetFile', $cabinetFile);

		if ($this->request->is('post')) {

			if (!Hash::get($this->request->data, 'use_auth_key', false)) {
				// 認証キーを使わない設定だったら、認証キーのPOST値を握りつぶす
				unset($this->request->data['AuthorizationKey']);
			}

			$this->CabinetFile->create();
			$this->request->data['CabinetFile']['cabinet_key'] = ''; // https://github.com/NetCommons3/NetCommons3/issues/7 対策

			// set status folderは常に公開
			$status = $this->Workflow->parseStatus();
			$this->request->data['CabinetFile']['status'] = $status;

			// set cabinet_id
			$this->request->data['CabinetFile']['cabinet_id'] = $this->_cabinet['Cabinet']['id'];
			// set language_id
			$this->request->data['CabinetFile']['language_id'] = Current::read('Language.id');
			// is_folderセット
			$this->request->data['CabinetFile']['is_folder'] = 0;
			//$this->request->data['CabinetFileTree']['parent_id'] = null;
			$this->request->data['CabinetFileTree']['cabinet_key'] = $this->_cabinet['Cabinet']['key'];

			// タイトルをファイル名にする
			$this->request->data['CabinetFile']['filename'] = $this->request->data['CabinetFile']['file']['name'];
			if (($this->CabinetFile->saveFile($this->request->data))) {

				$parentFolder = $this->CabinetFileTree->findById(
					$this->request->data['CabinetFileTree']['parent_id']
				);
				$url = NetCommonsUrl::actionUrl(
					array(
						'controller' => 'cabinet_files',
						'action' => 'index',
						'block_id' => Current::read('Block.id'),
						'frame_id' => Current::read('Frame.id'),
						'key' => Hash::get($parentFolder, 'CabinetFile.key', null)
					)
				);

				return $this->redirect($url);
			}

			$this->NetCommons->handleValidationError($this->CabinetFile->validationErrors);

		} else {
			$this->request->data = $cabinetFile;
			$this->request->data['CabinetFileTree']['parent_id'] = Hash::get(
				$this->request->named,
				'parent_id',
				null
			);
		}

		$parentId = $this->request->data['CabinetFileTree']['parent_id'];
		if ($parentId > 0) {
			$folderPath = $this->CabinetFileTree->getPath($parentId, null, 0);
		} else {
			$folderPath = [];
		}

		$folderPath[] = [
			'CabinetFile' => [
				'filename' => __d('cabinets', 'Add File')
			]
		];
		$this->set('folderPath', $folderPath);

		//$this->render('form');
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @throws ForbiddenException
 * @throws InternalErrorException
 * @return void
 */
	public function edit() {
		$this->set('isEdit', true);
		//$key = $this->request->params['named']['key'];
		$key = $this->request->params['pass'][1];

		//  keyのis_latstを元に編集を開始
		$cabinetFile = $this->CabinetFile->findByKeyAndIsLatest($key, 1);
		if (empty($cabinetFile)) {
			//  404 NotFound
			throw new NotFoundException();
		}
		// フォルダならエラー
		if ($cabinetFile['CabinetFile']['is_folder'] == true) {
			throw new InternalErrorException();
		}

		$treeId = $cabinetFile['CabinetFileTree']['id'];
		$folderPath = $this->CabinetFileTree->getPath($treeId, null, 0);
		$this->set('folderPath', $folderPath);

		if ($this->request->is(array('post', 'put'))) {

			$this->CabinetFile->create();
			//$this->request->data['CabinetFile']['cabinet_key'] = ''; // https://github.com/NetCommons3/NetCommons3/issues/7 対策

			// set status folderは常に公開
			$status = $this->Workflow->parseStatus();

			// ファイル名変更
			if ($this->request->data['CabinetFile']['file']['error'] == UPLOAD_ERR_NO_FILE) {
				// 新たなアップロードがなければ元の拡張子をつける。
				list($withOutExtFileName, $ext) = $this->CabinetFile->splitFileName(
					$cabinetFile['CabinetFile']['filename']
				);
			} else {
				// 新たなアップロードがあれば新たなファイルの拡張子をつける
				$ext = pathinfo(
					$this->request->data['CabinetFile']['file']['name'],
					PATHINFO_EXTENSION
				);
			}
			if ($ext !== null) {
				$this->request->data['CabinetFile']['filename'] .= '.' . $ext;
			}

			$this->request->data['CabinetFile']['status'] = $status;

			// set cabinet_id
			$this->request->data['CabinetFile']['cabinet_id'] = $this->_cabinet['Cabinet']['id'];
			// set language_id
			$this->request->data['CabinetFile']['language_id'] = Current::read('Language.id');

			$data = Hash::merge($cabinetFile, $this->request->data);

			if (!Hash::get($this->request->data, 'CabinetFile.use_auth_key', false)) {
				// 認証キーを使わない設定だったら、認証キーのPOST値を握りつぶす
				unset($data['AuthorizationKey']);
			}

			unset($data['CabinetFile']['id']); // 常に新規保存

			if ($this->CabinetFile->saveFile($data)) {
				$parentFolder = $this->CabinetFileTree->findById(
					$this->request->data['CabinetFileTree']['parent_id']
				);
				$url = NetCommonsUrl::actionUrl(
					array(
						'controller' => 'cabinet_files',
						'action' => 'index',
						'block_id' => Current::read('Block.id'),
						'frame_id' => Current::read('Frame.id'),
						'key' => Hash::get($parentFolder, 'CabinetFile.key', null)
					)
				);

				return $this->redirect($url);
			}

			$this->NetCommons->handleValidationError($this->CabinetFile->validationErrors);

		} else {

			$this->request->data = $cabinetFile;
			// 拡張子はとりのぞいておく
			list($withOutExtFileName, $ext) = $this->CabinetFile->splitFileName(
				$cabinetFile['CabinetFile']['filename']
			);
			$this->request->data['CabinetFile']['filename'] = $withOutExtFileName;
			if ($this->CabinetFile->canEditWorkflowContent($cabinetFile) === false) {
				throw new ForbiddenException(__d('net_commons', 'Permission denied'));
			}

		}

		$this->set('cabinetFile', $cabinetFile);
		$this->set('isDeletable', $this->CabinetFile->canDeleteWorkflowContent($cabinetFile));

		$this->render('form');
	}

/**
 * add method
 *
 * @return void
 */
	public function add_folder() {
		$this->set('isEdit', false);

		$cabinetFile = $this->CabinetFile->getNew();
		$this->set('cabinetFile', $cabinetFile);

		if ($this->request->is('post')) {
			$this->CabinetFile->create();
			$this->request->data['CabinetFile']['cabinet_key'] = ''; // https://github.com/NetCommons3/NetCommons3/issues/7 対策

			// set status folderは常に公開
			$status = WorkflowComponent::STATUS_PUBLISHED;
			$this->request->data['CabinetFile']['status'] = $status;

			// set cabinet_id
			$this->request->data['CabinetFile']['cabinet_id'] = $this->_cabinet['Cabinet']['id'];
			// set language_id
			$this->request->data['CabinetFile']['language_id'] = Current::read('Language.id');
			// is_folderセット
			$this->request->data['CabinetFile']['is_folder'] = 1;
			//$this->request->data['CabinetFileTree']['parent_id'] = null;
			$this->request->data['CabinetFileTree']['cabinet_key'] = $this->_cabinet['Cabinet']['key'];

			if (($result = $this->CabinetFile->saveFile($this->request->data))) {
				$url = NetCommonsUrl::actionUrl(
					array(
						'controller' => 'cabinet_files',
						'action' => 'folder_detail',
						'block_id' => Current::read('Block.id'),
						'frame_id' => Current::read('Frame.id'),
						'key' => $result['CabinetFile']['key']
					)
				);
				return $this->redirect($url);
			}

			$this->NetCommons->handleValidationError($this->CabinetFile->validationErrors);

		} else {
			$this->request->data = $cabinetFile;
			$this->request->data['CabinetFileTree']['parent_id'] = Hash::get(
				$this->request->named,
				'parent_id',
				null
			);
		}

		$parentId = $this->request->data['CabinetFileTree']['parent_id'];
		if ($parentId > 0) {
			$folderPath = $this->CabinetFileTree->getPath($parentId, null, 0);
		} else {
			$folderPath = [];
		}

		$folderPath[] = [
			'CabinetFile' => [
				'filename' => __d('cabinets', 'Add Folder')
			]
		];
		$this->set('folderPath', $folderPath);

		$this->render('folder_form');
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @throws ForbiddenException
 * @throws InternalErrorException
 * @return void
 */
	public function edit_folder() {
		$this->set('isEdit', true);
		//$key = $this->request->params['named']['key'];
		$key = $this->request->params['pass'][1];

		//  keyのis_latstを元に編集を開始
		$cabinetFile = $this->CabinetFile->findByKeyAndIsLatest($key, 1);
		if (empty($cabinetFile)) {
			//  404 NotFound
			throw new NotFoundException();
		}
		if ($cabinetFile['CabinetFile']['is_folder'] == false) {
			throw new InternalErrorException();
		}

		$treeId = $cabinetFile['CabinetFileTree']['id'];
		$folderPath = $this->CabinetFileTree->getPath($treeId, null, 0);
		$this->set('folderPath', $folderPath);

		if ($this->request->is(array('post', 'put'))) {

			$this->CabinetFile->create();
			//$this->request->data['CabinetFile']['cabinet_key'] = ''; // https://github.com/NetCommons3/NetCommons3/issues/7 対策

			// set status folderは常に公開
			$status = WorkflowComponent::STATUS_PUBLISHED;
			$this->request->data['CabinetFile']['status'] = $status;

			// set cabinet_id
			$this->request->data['CabinetFile']['cabinet_id'] = $this->_cabinet['Cabinet']['id'];
			// set language_id
			$this->request->data['CabinetFile']['language_id'] = Current::read('Language.id');

			$data = Hash::merge($cabinetFile, $this->request->data);

			unset($data['CabinetFile']['id']); // 常に新規保存

			if ($this->CabinetFile->saveFile($data)) {
				$url = NetCommonsUrl::actionUrl(
					array(
						'controller' => 'cabinet_files',
						'action' => 'folder_detail',
						'frame_id' => Current::read('Frame.id'),
						'block_id' => Current::read('Block.id'),
						'key' => $data['CabinetFile']['key']
					)
				);

				return $this->redirect($url);
			}

			$this->NetCommons->handleValidationError($this->CabinetFile->validationErrors);

		} else {

			$this->request->data = $cabinetFile;
			if ($this->CabinetFile->canEditWorkflowContent($cabinetFile) === false) {
				throw new ForbiddenException(__d('net_commons', 'Permission denied'));
			}

		}

		$this->set('cabinetFile', $cabinetFile);
		$this->set('isDeletable', $this->CabinetFile->canDeleteWorkflowContent($cabinetFile));

		$this->render('folder_form');
	}

/**
 * フォルダ選択画面
 *
 * @return void
 */
	public function select_folder() {
		//$currentTreeId = Hash::get($this->request->named, 'parent_tree_id', null);
		$key = isset($this->request->params['pass'][1]) ? $this->request->params['pass'][1] : null;
		$conditions = [
			'CabinetFile.key' => $key,
			'CabinetFile.cabinet_id' => $this->_cabinet['Cabinet']['id']
		];
		$conditions = $this->CabinetFile->getWorkflowConditions($conditions);
		$cabinetFile = $this->CabinetFile->find('first', ['conditions' => $conditions]);
		$currentTreeId = $cabinetFile['CabinetFileTree']['parent_id'];

		//$targetFileKey =

		$this->set('currentTreeId', $currentTreeId);
		//レイアウトの設定
		$this->viewClass = 'View';
		$this->layout = 'NetCommons.modal';

		// 全フォルダツリーを得る
		$conditions = [
			'is_folder' => 1,
			'cabinet_key' => $this->_cabinet['Cabinet']['key'],
		];
		if ($cabinetFile['CabinetFile']['is_folder']) {
			$conditions['NOT'] = array(
				'AND' => array(
					'CabinetFileTree.lft >=' => $cabinetFile['CabinetFileTree']['lft'],
					'CabinetFileTree.rght <=' => $cabinetFile['CabinetFileTree']['rght']
				)
			);
		}

		$folders = $this->CabinetFileTree->find(
			'threaded',
			['conditions' => $conditions, 'recursive' => 0, 'order' => 'CabinetFile.filename ASC']
		);
		$this->set('folders', $folders);

		// カレントフォルダのツリーパスを得る
		if ($currentTreeId > 0) {
			$folderPath = $this->CabinetFileTree->getPath($currentTreeId, null, 0);
			$this->set('folderPath', $folderPath);
			$nestCount = count($folderPath);
			if ($nestCount > 1) {
				// 親フォルダあり
				$url = NetCommonsUrl::actionUrl(
					[
						'key' => $folderPath[$nestCount - 2]['CabinetFile']['key'],
						'block_id' => Current::read('Block.id'),
						'frame_id' => Current::read('Frame.id'),
					]
				);

			} else {
				// 親はキャビネット
				$url = NetCommonsUrl::backToIndexUrl();
			}
			$this->set('parentUrl', $url);
		} else {
			// ルート
			$this->set('folderPath', array());
			$this->set('parentUrl', false);
		}
	}

/**
 * ファイル・フォルダ移動
 *
 * @return void
 */
	public function move() {
		if ($this->request->is(array('post', 'put'))) {
			$key = $this->params['pass'][1];

			//  keyのis_latstを元に編集を開始
			$cabinetFile = $this->CabinetFile->findByKeyAndIsLatest($key, 1);
			$parentId = Hash::get($this->request->named, 'parent_id', null);

			$cabinetFile['CabinetFileTree']['parent_id'] = $parentId;

			$result = $this->CabinetFileTree->save($cabinetFile);

			if ($result) {
				//正常の場合
				//if($cabinetFile['CabinetFile']['is_folder']) {
				// reloadするのでSession::flash
				//$this->Flash->set(__d('cabinets', '移動しました'), );
				//$this->Session->setFlash('移動しました');

				//}else{
				$this->NetCommons->setFlashNotification(
					__d('cabinets', 'Moved.'),
					array(
						'class' => 'success',
						'ajax' => !$cabinetFile['CabinetFile']['is_folder']
					)
				);
				//}
			} else {
				$this->NetCommons->setFlashNotification(
					__d('cabinets', 'Move failed'),
					array(
						'class' => 'danger',
					)
				);
				//$this->NetCommons->handleValidationError($this->RolesRoomsUser->validationErrors);
			}

			//$this->set('_serialize', ['message']);
		}
	}

/**
 * フォルダパスをJsonで返す
 *
 * @return void
 */
	public function get_folder_path() {
		$treeId = Hash::get($this->request->named, 'tree_id', null);
		$folderPath = $this->CabinetFileTree->getPath($treeId, null, 0);
		//foreach($folderPath as &$folder){
		//	$folder['url'] =
		//}
		$this->set('folderPath', $folderPath);
		$this->set('_serialize', ['folderPath']);
	}

/**
 * delete method
 *
 * @throws ForbiddenException
 * @throws InternalErrorException
 * @return void
 */
	public function delete() {
		$key = $this->request->data['CabinetFile']['key'];

		$this->request->allowMethod('post', 'delete');

		$conditions = [
			'CabinetFile.key' => $key,
			'CabinetFile.is_latest' => 1,
		];
		$cabinetFile = $this->CabinetFile->find('first', ['conditions' => $conditions]);

		// 権限チェック
		if ($this->CabinetFile->canDeleteWorkflowContent($cabinetFile) === false) {
			throw new ForbiddenException(__d('net_commons', 'Permission denied'));
		}

		if ($this->CabinetFile->deleteFileByKey($key) === false) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		return $this->redirect(
			NetCommonsUrl::actionUrl(
				array(
					'controller' => 'cabinet_files',
					'action' => 'index',
					'frame_id' => Current::read('Frame.id'),
					'block_id' => Current::read('Block.id')
				)
			)
		);
	}

/**
 * unzip action
 *
 * @return void
 */
	public function unzip() {
		$key = $this->request->params['pass'][1];

		$options = [
			'conditions' => [
				'CabinetFile.key' => $key,
				'CabinetFile.cabinet_id' => $this->_cabinet['Cabinet']['id']
			]
		];

		$cabinetFile = $this->CabinetFile->find('first', $options);

		if ($cabinetFile && $cabinetFile['UploadFile']['file']['extension'] == 'zip') {
			if (!$this->CabinetFile->unzip($cabinetFile)) {
				// Validate error
				$message = implode("<br />", $this->CabinetFile->validationErrors);
				$this->NetCommons->setFlashNotification(
					$message,
					[
						'class' => 'danger',
						//'interval' => NetCommonsComponent::ALERT_VALIDATE_ERROR_INTERVAL,
					]
				);
			}
			$parentCabinetFolder = $this->CabinetFile->getParent($cabinetFile);
			$this->redirect(
				NetCommonsUrl::actionUrl(
					[
						'controller' => 'cabinet_files',
						'action' => 'index',
						'key' => $parentCabinetFolder['CabinetFile']['key'],
						'block_id' => Current::read('Block.id'),
					]
				)
			);
		} else {
			return $this->throwBadRequest();
		}
	}
}

