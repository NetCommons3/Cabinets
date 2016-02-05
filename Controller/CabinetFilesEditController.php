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
		$this->set('isEdit', false);

		$cabinetFile = $this->CabinetFile->getNew();
		$this->set('cabinetFile', $cabinetFile);

		if ($this->request->is('post')) {
			$this->CabinetFile->create();
			$this->request->data['CabinetFile']['cabinet_key'] = ''; // https://github.com/NetCommons3/NetCommons3/issues/7 対策

			// set status
			$status = $this->Workflow->parseStatus();
			$this->request->data['CabinetFile']['status'] = $status;

			// set cabinet_id
			$this->request->data['CabinetFile']['cabinet_id'] = $this->_cabinet['Cabinet']['id'];
			// set language_id
			$this->request->data['CabinetFile']['language_id'] = $this->viewVars['languageId'];
			if (($result = $this->CabinetFile->saveFile(Current::read('Block.id'), Current::read('Frame.id'), $this->request->data))) {
				$url = NetCommonsUrl::actionUrl(
					array(
						'controller' => 'cabinet_files',
						'action' => 'view',
						'block_id' => Current::read('Block.id'),
						'frame_id' => Current::read('Frame.id'),
						'key' => $result['CabinetFile']['key'])
				);
				return $this->redirect($url);
			}

			$this->NetCommons->handleValidationError($this->CabinetFile->validationErrors);

		} else {
			$this->request->data = $cabinetFile;
			$this->request->data['Tag'] = array();
		}

		$this->render('form');
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
		$key = $this->params['pass'][1];

		//  keyのis_latstを元に編集を開始
		$cabinetFile = $this->CabinetFile->findByKeyAndIsLatest($key, 1);
		if (empty($cabinetFile)) {
			//  404 NotFound
			throw new NotFoundException();
		}

		if ($this->request->is(array('post', 'put'))) {

			$this->CabinetFile->create();
			$this->request->data['CabinetFile']['cabinet_key'] = ''; // https://github.com/NetCommons3/NetCommons3/issues/7 対策

			// set status
			$status = $this->Workflow->parseStatus();
			$this->request->data['CabinetFile']['status'] = $status;

			// set cabinet_id
			$this->request->data['CabinetFile']['cabinet_id'] = $this->_cabinet['Cabinet']['id'];
			// set language_id
			$this->request->data['CabinetFile']['language_id'] = $this->viewVars['languageId'];

			$data = $this->request->data;

			unset($data['CabinetFile']['id']); // 常に新規保存

			if ($this->CabinetFile->saveFile(Current::read('Block.id'), Current::read('Frame.id'), $data)) {
				$url = NetCommonsUrl::actionUrl(
					array(
						'controller' => 'cabinet_files',
						'action' => 'view',
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

		$comments = $this->CabinetFile->getCommentsByContentKey($cabinetFile['CabinetFile']['key']);
		$this->set('comments', $comments);

		$this->render('form');
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
		$key = $this->params['pass'][1];

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
			$this->request->data['CabinetFile']['language_id'] = $this->viewVars['languageId'];

			$data = $this->request->data;

			unset($data['CabinetFile']['id']); // 常に新規保存

			if ($this->CabinetFile->saveFile(Current::read('Block.id'), Current::read('Frame.id'), $data)) {
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

		$comments = $this->CabinetFile->getCommentsByContentKey($cabinetFile['CabinetFile']['key']);
		$this->set('comments', $comments);

		$this->render('folder_form');
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
		debug($savedData);
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

}

