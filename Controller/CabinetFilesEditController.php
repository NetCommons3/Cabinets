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
			if (($result = $this->CabinetFile->saveFile($this->request->data))) {

				$parentFolder = $this->CabinetFileTree->findById($this->request->data['CabinetFileTree']['parent_id']);
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
			$this->request->data['CabinetFileTree']['parent_id'] = Hash::get($this->request->named, 'parent_id', null);
		}

		$parentId = $this->request->data['CabinetFileTree']['parent_id'];
		if($parentId > 0){
			$folderPath = $this->CabinetFileTree->getPath($parentId, null, 0);
		}else{
			$folderPath = [];
		}

		$folderPath[] = [
			'CabinetFile' => [
				'filename' => __d('cabinets', '新規ファイル')
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

			$this->request->data['CabinetFile']['status'] = $status;

			// set cabinet_id
			$this->request->data['CabinetFile']['cabinet_id'] = $this->_cabinet['Cabinet']['id'];
			// set language_id
			$this->request->data['CabinetFile']['language_id'] = Current::read('Language.id');

			$data = Hash::merge($cabinetFile, $this->request->data);

			unset($data['CabinetFile']['id']); // 常に新規保存

			if ($this->CabinetFile->saveFile($data)) {
				$parentFolder = $this->CabinetFileTree->findById($this->request->data['CabinetFileTree']['parent_id']);
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
						'key' => $result['CabinetFile']['key'])
				);
				return $this->redirect($url);
			}

			$this->NetCommons->handleValidationError($this->CabinetFile->validationErrors);

		} else {
			$this->request->data = $cabinetFile;
			$this->request->data['CabinetFileTree']['parent_id'] = Hash::get($this->request->named, 'parent_id', null);
		}

		$parentId = $this->request->data['CabinetFileTree']['parent_id'];
		if($parentId > 0){
			$folderPath = $this->CabinetFileTree->getPath($parentId, null, 0);
		}else{
			$folderPath = [];
		}

		$folderPath[] = [
				'CabinetFile' => [
					'filename' => __d('cabinets', '新規フォルダ')
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
		if($cabinetFile['CabinetFile']['is_folder']){
			$conditions['NOT'] = array(
				'AND' => array(
					'CabinetFileTree.lft >=' => $cabinetFile['CabinetFileTree']['lft'],
					'CabinetFileTree.rght <=' => $cabinetFile['CabinetFileTree']['rght']
				)
			);
		}

		$folders = $this->CabinetFileTree->find('threaded', ['conditions' => $conditions, 'recursive' => 0, 'order' => 'CabinetFile.filename ASC']);
		$this->set('folders', $folders);


		// カレントフォルダのツリーパスを得る
		if($currentTreeId > 0){
			$folderPath = $this->CabinetFileTree->getPath($currentTreeId, null, 0);
			$this->set('folderPath', $folderPath);
			$nestCount = count($folderPath);
			if($nestCount > 1){
				// 親フォルダあり
				$url = NetCommonsUrl::actionUrl(
					[
						'key' => $folderPath[$nestCount - 2]['CabinetFile']['key'],
						'block_id' => Current::read('Block.id'),
						'frame_id' => Current::read('Frame.id'),
					]
				);

			}else{
				// 親はキャビネット
				$url = NetCommonsUrl::backToIndexUrl();
			}
			$this->set('parentUrl', $url);
		}else{
			// ルート
			$this->set('folderPath', array());
			$this->set('parentUrl', false);
		}
	}

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
					$this->NetCommons->setFlashNotification(__d('cabinets', '移動しました'), array(
						'class' => 'success',
						'ajax' => !$cabinetFile['CabinetFile']['is_folder']
					));
				//}
			} else {
				$this->NetCommons->setFlashNotification(__d('cabinets', '移動失敗'), array(
					'class' => 'danger',
				));
				//$this->NetCommons->handleValidationError($this->RolesRoomsUser->validationErrors);
			}

			//$this->set('_serialize', ['message']);
		}

	}

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

		$cabinetFile = $this->CabinetFile->findByKeyAndIsLatest($key, 1);

		// 権限チェック
		if ($this->CabinetFile->canDeleteWorkflowContent($cabinetFile) === false) {
			throw new ForbiddenException(__d('net_commons', 'Permission denied'));
		}

		if ($this->CabinetFile->deleteFileByKey($key) === false) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		return $this->redirect(
			NetCommonsUrl::actionUrl(
				array('controller' => 'cabinet_files', 'action' => 'index', 'frame_id' => Current::read('Frame.id'), 'block_id' => Current::read('Block.id'))));
	}

	public function import() {
		App::uses('CsvFileReader', 'Files.Utility');
		if ($this->request->is(array('post', 'put'))) {
			$file = $this->FileUpload->getTemporaryUploadFile('import_csv');
			debug($file);
			$reader = new CsvFileReader($file);
			foreach($reader as $row){
				debug($row);
			}
		}
	}

	public function regist() {
		$UploadFile = ClassRegistry::init('Files.UploadFile');
		$path = '/var/www/app/app/Plugin/Files/Test/Fixture/logo.gif';
		$path2 = TMP . 'logo.gif';
		copy($path, $path2);
		$UploadFile->registByFilePath($path2, 'cabinets', 'content_key..', 'photo');
	}

	public function attach_file() {
		$UploadFile = ClassRegistry::init('Files.UploadFile');
		$path = '/var/www/app/app/Plugin/Files/Test/Fixture/logo.gif';
		$path2 = TMP . 'logo.gif';
		copy($path, $path2);
		$data = $this->CabinetFile->findByIsLatest(true);
		//$file = new File($path2);
		$this->CabinetFile->attachFile($data, 'pdf', $path2);

		$savedData = $this->CabinetFile->findById($data['CabinetFile']['id']);
	}

	public function temporary_download() {
		App::uses('TemporaryFile', 'Files.Utility');
		$file = new TemporaryFile();
		$file->append('test');

		//$this->Download = $this->Components->load('Files.Download');
		//return $this->Download->downloadFile($file, ['name', 'test.txt']);

		$this->response->file($file->path, ['name' => 'test.txt']);
		return $this->response;
	}


	/**
	 * 配列のCSV出力例
	 *
	 * @return CakeResponse|null
	 */
	public function csv_download2() {

		if ($this->request->is(array('post', 'put'))) {
			App::uses('CsvFileWriter', 'Files.Utility');

			$data = [
					['データID', 'タイトル', '本文', '作成日時'],
					[1, '薪だなつくりました', '薪だなつくるの大変だったよ', '2015-10-01 10:00:00'],
					[2, '薪ストーブ点火', '寒くなってきたので薪ストーブに火入れましたよ', '2015-12-01 13:00:00'],
			];
			$csvWriter = new CsvFileWriter();
			foreach($data as $line){
				$csvWriter->add($line);
			}
			$csvWriter->close();

			return $csvWriter->download('export.csv');
		}

	}

	/**
	 * Modelから取得したデータの指定カラムだけCSV出力する例
	 *
	 * @return CakeResponse
	 */
	public function csv_download3() {
		App::uses('CsvFileWriter', 'Files.Utility');

		$header = [
			'CabinetFile.id' => 'データID',
			'CabinetFile.title' => 'タイトル',
			'CabinetFile.body1' => '本文1',
			'CabinetFile.publish_start' => '公開日時'
		];
		$result = $this->CabinetFile->find('all');

		$csvWriter = new CsvFileWriter(['header' => $header]);
		foreach($result as $data){
			$csvWriter->addModelData($data);
		}
		$csvWriter->close();

		//$zip = new ZipArchive();
		//$tmpFile = new TemporaryFile();
		//$zip->open($tmpFile->path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
		//$zip->addFile($csvWriter->path);
		//$zip->close();

		// パスワード


		//return $csvWriter->download('export.csv');
		return $csvWriter->zipDownload('test.zip', '日本語ファイル名.csv', 'pass');
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

		if($cabinetFile && $cabinetFile['UploadFile']['file']['extension'] == 'zip'){
			$this->CabinetFile->unzip($cabinetFile);
			$parentCabinetFolder = $this->CabinetFile->getParent($cabinetFile);
			$this->redirect(NetCommonsUrl::actionUrl([
				'controller' => 'cabinet_files',
				'action' => 'index',
				'key' => $parentCabinetFolder['CabinetFile']['key'],
				'block_id' => Current::read('Block.id'),
			]));
		} else {
			return $this->throwBadRequest();
		}

	}

}

