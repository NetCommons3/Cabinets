<?php
/**
 * CabinetEtnriesController
 */
App::uses('CabinetsAppController', 'Cabinets.Controller');
App::uses('ZipDownloader', 'Files.Utility');
App::uses('TemporaryFolder', 'Files.Utility');

/**
 * CabinetFiles Controller
 *
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @author   Kazunori Sakamoto <exkazuu@willbooster.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 * @property NetCommonsWorkflow $NetCommonsWorkflow
 * @property PaginatorComponent $Paginator
 * @property CabinetFile $CabinetFile
 */
class CabinetFilesController extends CabinetsAppController {

/**
 * @var array use models
 */
	public $uses = array(
		'Cabinets.CabinetFile',
		'Cabinets.CabinetFileTree',
		'Workflow.WorkflowComment',
	);

/**
 * @var array helpers
 * @var array helpers
 */
	public $helpers = array(
		'NetCommons.BackTo',
		'Workflow.Workflow',
		'Users.DisplayUser',
		'Paginator',
		'NetCommons.Token',
		'Cabinets.CabinetFile',
	);

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'Paginator',
		//'NetCommons.NetCommonsWorkflow',
		//'NetCommons.NetCommonsRoomRole' => array(
		//	//コンテンツの権限設定
		//	'allowedActions' => array(
		//		'contentEditable' => array('edit', 'add'),
		//		'contentCreatable' => array('edit', 'add'),
		//	),
		//),
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				//'add,edit,delete' => 'content_creatable',
				//'reply' => 'content_comment_creatable',
				//'approve' => 'content_comment_publishable',
			),
		),
		'Files.Download',
		'AuthorizationKeys.AuthorizationKey' => [
			'operationType' => 'popup',
			//'operationType' => 'redirect',
			//'operationType' => 'redirect',
			'targetAction' => 'download',
			'model' => 'CabinetFile',
		],
	);

/**
 * @var array 絞り込みフィルタ保持値
 */
	protected $_filter = array(
		'categoryId' => 0,
		'status' => 0,
		'yearMonth' => 0,
	);

/**
 * @var array Cabinet
 */
	protected $_cabinet;

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		// ゲストアクセスOKのアクションを設定
		$this->Auth->allow(
			'index',
			'view',
			'folder_detail',
			'download',
			'download_folder',
			'get_download_counts',
			'check_download_folder'
		);
		parent::beforeFilter();
		$this->_cabinet = $this->Cabinet->find('first', array(
			'recursive' => 0,
			'conditions' => $this->Cabinet->getBlockConditionById(),
		));
		$this->set('cabinet', $this->_cabinet);
	}

/**
 * index
 *
 * @return void
 */
	public function index() {
		if (!Current::read('Block.id')) {
			$this->autoRender = false;
			return;
		}
		//$this->CabinetFileTree->recover('parent');

		// currentFolderを取得
		//$folderKey = isset($this->request->params['pass'][1]) ? $this->request->params['pass'][1] : null;
		$folderKey = Hash::get($this->request->params, 'key', null);
		$currentFolder = $this->_getCurrentFolder($folderKey);
		$currentTreeId = $currentFolder['CabinetFileTree']['id'];

		$this->set('currentFolder', $currentFolder);
		$this->set('currentTreeId', $currentTreeId);

		// 全フォルダツリーを得る
		$conditions = [
			'CabinetFile.is_folder' => 1,
			'CabinetFileTree.cabinet_key' => $this->viewVars['cabinet']['Cabinet']['key']
		];
		$folders = $this->CabinetFileTree->find(
			'threaded',
			['conditions' => $conditions, 'recursive' => 0, 'order' => 'CabinetFile.filename ASC']
		);
		$this->set('folders', $folders);

		$files = $this->_getCurrentFolderFiles($currentTreeId);
		$this->set('cabinetFiles', $files);

		// カレントフォルダのツリーパスを得る
		$folderPath = $this->CabinetFileTree->getPath($currentTreeId, null, 0);
		$this->set('folderPath', $folderPath);

		$url = $this->_getParentFolderUrl($currentFolder, $folderPath);
		$this->set('parentUrl', $url);

		$this->set('listTitle', $this->_cabinetTitle);
	}

/**
 * フォルダ詳細
 *
 * @return void
 */
	public function folder_detail() {
		$folderKey = Hash::get($this->request->params, 'key', null);

		$conditions = [
			'CabinetFile.key' => $folderKey,
			'CabinetFile.cabinet_key' => $this->_cabinet['Cabinet']['key']
		];
		$conditions = $this->CabinetFile->getWorkflowConditions($conditions);
		$cabinetFile = $this->CabinetFile->find('first', ['conditions' => $conditions]);
		if ($cabinetFile) {
			// folderじゃなかったらエラー
			if (!$cabinetFile['CabinetFile']['is_folder']) {
				return $this->throwBadRequest();
			}

			$cabinetFile['CabinetFile']['size'] = $this->CabinetFile->getTotalSizeByFolder(
				$cabinetFile
			);
			$cabinetFile['CabinetFile']['has_children'] = $this->CabinetFile->hasChildren($cabinetFile);

			//$cabinetFile['CabinetFile']['size'] =

			$this->set('cabinetFile', $cabinetFile);

			$this->_setFolderPath($cabinetFile);
		} else {
			return $this->throwBadRequest();
		}
	}

/**
 * フォルダパスをViewにセット
 *
 * @param array $cabinetFile CabinetFileデータ
 * @return void
 */
	protected function _setFolderPath($cabinetFile) {
		$treeId = $cabinetFile['CabinetFileTree']['id'];
		$folderPath = $this->CabinetFileTree->getPath($treeId, null, 0);
		$this->set('folderPath', $folderPath);
	}

/**
 * 権限の取得
 *
 * @return array
 */
	protected function _getPermission() {
		$permissionNames = array(
			'content_readable',
			'content_creatable',
			'content_editable',
			'content_publishable',
		);
		$permission = array();
		foreach ($permissionNames as $key) {
			$permission[$key] = Current::permission($key);
		}
		return $permission;
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @return void
 */
	public function view() {
		$folderKey = Hash::get($this->request->params, 'key', null);
		$conditions = [
			'CabinetFile.key' => $folderKey,
			'CabinetFile.cabinet_key' => $this->_cabinet['Cabinet']['key']
		];
		$conditions = $this->CabinetFile->getWorkflowConditions($conditions);
		$cabinetFile = $this->CabinetFile->find('first', ['conditions' => $conditions]);

		if ($cabinetFile) {
			// ファイルでなければエラー
			if ($cabinetFile['CabinetFile']['is_folder']) {
				return $this->throwBadRequest();
			}

			$this->set('cabinetFile', $cabinetFile);

			$this->_setFolderPath($cabinetFile);

			//新着データを既読にする
			$this->CabinetFile->saveTopicUserStatus($cabinetFile);
		} else {
			return $this->throwBadRequest();
		}
	}

/**
 * ファイルダウンロード
 *
 * @throws NotFoundException
 * @return mixed
 */
	public function download() {
		// ここから元コンテンツを取得する処理
		$folderKey = Hash::get($this->request->params, 'key', null);
		$conditions = [
			'CabinetFile.key' => $folderKey,
			'CabinetFile.cabinet_key' => $this->_cabinet['Cabinet']['key']
		];
		$conditions = $this->CabinetFile->getWorkflowConditions($conditions);
		$cabinetFile = $this->CabinetFile->find('first', ['conditions' => $conditions]);
		//$this->set('cabinetFile', $cabinetFile);
		// ここまで元コンテンツを取得する処理

		$this->AuthorizationKey->guard('popup', 'CabinetFile', $cabinetFile);

		// ダウンロード実行
		if ($cabinetFile) {
			return $this->Download->doDownload(
				$cabinetFile['CabinetFile']['id'],
				[
					'field' => 'file',
					'download' => true,
					'name' => $cabinetFile['CabinetFile']['filename'],
				]
			);
		} else {
			// 表示できないファイルへのアクセスなら404
			throw new NotFoundException(__d('cabinets', 'Invalid cabinet file'));
		}
	}

/**
 * フォルダのZIPダウンロード
 *
 * @return CakeResponse|string|void
 */
	public function download_folder() {
		if (! $this->request->is('post')) {
			return $this->throwBadRequest();
		}
		// フォルダを取得
		$folderKey = Hash::get($this->request->data, 'CabinetFile.key', null);
		$conditions = [
			'CabinetFile.key' => $folderKey,
			'CabinetFile.cabinet_key' => $this->_cabinet['Cabinet']['key']
		];
		$conditions = $this->CabinetFile->getWorkflowConditions($conditions);
		$cabinetFolder = $this->CabinetFile->find('first', ['conditions' => $conditions]);

		$tmpFolder = new TemporaryFolder();
		try {
			$this->_prepareDownload($tmpFolder->path, $cabinetFolder);
		} catch (BadRequestException $e) {
			//先にcheck_download_folderでチェックしているため、この処理は基本通らない
			//もしタイムラグでダウンロードパスワードが設定されたら、フラッシュメッセージを表示して、
			//ブラウザをリフレッシュする
			$this->NetCommons->setFlashNotification($e->getMessage(), [], $e->getCode());
			return $this->redirect($this->referer());
		}
		$zipDownloader = $this->_getZipDownloader();

		list($folders, $files) = $tmpFolder->read(true, false, true);
		foreach ($folders as $folder) {
			$zipDownloader->addFolder($folder);
		}
		foreach ($files as $file) {
			$zipDownloader->addFile($file);
		}
		return $zipDownloader->download($cabinetFolder['CabinetFile']['filename'] . '.zip');
	}

/**
 * ファイルのダウンロード数の取得
 *
 * @return void
 */
	public function get_download_counts() {
		$query = [
			'fields' => [
				'UploadFile.id',
				'UploadFile.total_download_count',
			],
			'conditions' => [
				'UploadFile.id' => explode(',', $this->request->query('upload_file_ids')),
			],
			'recursive' => -1
		];
		$UploadFile = ClassRegistry::init('Files.UploadFile');
		$files = $UploadFile->find('all', $query);

		$this->set('_serialize', ['counts']);
		$this->set('counts', $files);
	}

/**
 * フォルダのZIPダウンロードができるか否かチェック
 *
 * @return void
 */
	public function check_download_folder() {
		if (! $this->request->is('post') || ! $this->request->is('ajax')) {
			return $this->throwBadRequest();
		}
		// フォルダを取得
		$folderKey = Hash::get($this->request->data, 'CabinetFile.key', null);
		$conditions = [
			'CabinetFile.key' => $folderKey,
			'CabinetFile.cabinet_key' => $this->_cabinet['Cabinet']['key']
		];
		$conditions = $this->CabinetFile->getWorkflowConditions($conditions);
		$cabinetFolder = $this->CabinetFile->find('first', ['conditions' => $conditions]);

		$this->_checkDownloadFolder($cabinetFolder);
	}

/**
 * 圧縮ダウンロード用のCabinetFileデータを取得
 *
 * @param array $cabinetFolder CabinetFileデータ 処理するフォルダ
 * @return array
 */
	protected function _findCabinetFilesByFolderDownload($cabinetFolder) {
		// フォルダのファイル取得
		$files = $this->CabinetFile->find(
			'all',
			[
				'conditions' => $this->CabinetFile->getWorkflowConditions(
					[
						'CabinetFileTree.parent_id' => $cabinetFolder['CabinetFileTree']['id'],
						//'CabinetFile.is_folder' => false,
					]
				)
			]
		);
		return $files;
	}

/**
 * フォルダのZIPダウンロード前処理
 *
 * @param string $path ダウンロード処理用テンポラリフォルダのカレントパス
 * @param array $cabinetFolder CabinetFileデータ 処理するフォルダ
 * @throws BadRequestException
 * @return void
 */
	protected function _prepareDownload($path, $cabinetFolder) {
		// フォルダのファイル取得
		$files = $this->_findCabinetFilesByFolderDownload($cabinetFolder);
		foreach ($files as $file) {
			if ($file['CabinetFile']['is_folder']) {
				mkdir($path . DS . $file['CabinetFile']['filename']);
				$this->_prepareDownload($path . DS . $file['CabinetFile']['filename'], $file);
			} else {
				if (isset($file['AuthorizationKey'])) {
					throw new BadRequestException(
						__d(
							'cabinets',
							'Folder that contains the files that are password is set can not be downloaded ZIP.'
						)
					);
				}
				// ダウンロードカウントアップ
				$this->CabinetFile->downloadCountUp($file, 'file');

				$filePath = $this->CabinetFile->getRealFilePath($file, 'file');

				//$filePath = WWW_ROOT .
				//	$file['UploadFile']['file']['path'] .
				//	$file['UploadFile']['file']['id'] .
				//	DS . $file['UploadFile']['file']['real_file_name'];
				copy($filePath, $path . DS . $file['CabinetFile']['filename']);
			}
		}
	}

/**
 * フォルダのZIPダウンロードができるか否かチェックする
 *
 * @param array $cabinetFolder CabinetFileデータ 処理するフォルダ
 * @throws BadRequestException
 * @return void
 */
	protected function _checkDownloadFolder($cabinetFolder) {
		// フォルダのファイル取得
		$files = $this->_findCabinetFilesByFolderDownload($cabinetFolder);
		foreach ($files as $file) {
			if ($file['CabinetFile']['is_folder']) {
				$this->_checkDownloadFolder($file);
			} else {
				if (isset($file['AuthorizationKey'])) {
					throw new BadRequestException(
						__d(
							'cabinets',
							'Folder that contains the files that are password is set can not be downloaded ZIP.'
						)
					);
				}
			}
		}
	}

/**
 * フォルダキーからフォルダを返す
 *
 * @param string $folderKey フォルダキー(CabinetFile.key)
 * @return array|null
 */
	protected function _getCurrentFolder($folderKey) {
		if (is_null($folderKey)) {
			// 指定がなければルートフォルダを取得する
			$currentFolder = $this->CabinetFile->getRootFolder($this->_cabinet);
			return $currentFolder;
		} else {
			$currentFolder = $this->CabinetFile->find(
				'first',
				[
					'conditions' => [
						'CabinetFile.key' => $folderKey,
						//'CabinetFile.language_id' => Current::read('Language.id'),
						'CabinetFile.is_latest' => true,
					]
				]
			);
			return $currentFolder;
		}
	}

/**
 * 親フォルダのURLを返す
 *
 * @param array $currentFolder 現在位置のCabinetFileデータ（フォルダ)
 * @param array $folderPath 現在位置までのTreeパス
 * @return null|string 親フォルダのURL
 */
	protected function _getParentFolderUrl($currentFolder, $folderPath) {
		// 親フォルダのTreeIDがルートフォルダのTreeIDと違うなら親フォルダは通常フォルダ
		$isRootFolder = ($currentFolder['CabinetFileTree']['parent_id'] === null);
		$hasParentFolder = ($currentFolder['CabinetFileTree']['parent_id'] !=
			$folderPath[0]['CabinetFileTree']['id']);

		if ($isRootFolder) {
			// root folder
			$url = null;
		} elseif ($hasParentFolder) {
			// 親フォルダあり
			$nestCount = count($folderPath);
			$url = NetCommonsUrl::actionUrl(
				[
					'key' => $folderPath[$nestCount - 2]['CabinetFile']['key'],
					'block_id' => Current::read('Block.id'),
					'frame_id' => Current::read('Frame.id'),
				]
			);
		} else {
			// 親はキャビネット（ルートフォルダ）
			$url = NetCommonsUrl::backToPageUrl(null, ['frame_id' => Current::read('Frame.id')]);

		}
		return $url;
	}

/**
 * ソートパラメータを返す
 *
 * @return array($sort, $direction)
 */
	protected function _getSortParams() {
		// ソートに使えるキーかチェック
		$allowSortKeys = [
			'filename',
			'size',
			'modified'
		];
		$sort = Hash::get($this->request->named, 'sort', 'filename');
		//  asc, descしか入力を許可しない
		$direction = Hash::get($this->request->named, 'direction', 'asc');
		if (!in_array($sort, $allowSortKeys) || !in_array($direction, ['asc', 'desc'])) {
			return $this->throwBadRequest();
		}
		return array($sort, $direction);
	}

/**
 * カレントフォルダのファイル・フォルダリストを返す
 *
 * @param int $currentTreeId 現在のCabinetFileTree.id
 * @return array CabinetFileの一覧
 */
	protected function _getCurrentFolderFiles($currentTreeId) {
		// カレントフォルダのファイル・フォルダリストを得る。
		$conditions = [
			'CabinetFileTree.parent_id' => $currentTreeId,
			'Cabinet.key' => $this->viewVars['cabinet']['Cabinet']['key']
		];
		//  workflowコンディションを混ぜ込む
		$conditions = $this->CabinetFile->getWorkflowConditions($conditions);
		// ソート順変更

		list($sort, $direction) = $this->_getSortParams();
		// ソート順変更リンクをPaginatorHelperで出力するときに必要な値をセットしておく。
		$this->request->params['paging'] = [
			'CabinetFile' => [
				'options' => [
					'sort' => $sort,
					'direction' => $direction
				],
				'paramType' => 'named',
			]
		];

		// 昇順のときフォルダが先、降順の時フォルダが後
		$folderDirection = ($direction === 'asc') ? 'desc' : 'asc';
		$order = [
			'is_folder' => $folderDirection
		];
		if ($sort != 'size') {
			$order['CabinetFile.' . $sort] = $direction;
		}
		$results = $this->CabinetFile->find(
			'all',
			[
				'conditions' => $conditions,
				'order' => $order
			]
		);

		$folders = array();
		$files = array();
		foreach ($results as &$file) {
			if ($file['CabinetFile']['is_folder']) {
				$file['CabinetFile']['size'] = $this->CabinetFile->getTotalSizeByFolder($file);
				$file['CabinetFile']['has_children'] = $this->CabinetFile->hasChildren($file);
				$folders[] = $file;
			} else {
				$file['CabinetFile']['size'] = $file['UploadFile']['file']['size'];
				$files[] = $file;
			}
		}

		if ($sort == 'size') {
			// sizeでのソートは自前でおこなう@
			$files = Hash::sort($files, '{n}.CabinetFile.size', $direction, 'numeric');
			// フォルダ・ファイルは別れるようにする。
			$folders = Hash::sort($folders, '{n}.CabinetFile.size', $direction, 'numeric');
		}
		if ($direction == 'asc') {
			// ascならフォルダ先 Hash::mergeだと上書きされてしまうのであえてarray_merge使用
			$files = array_merge($folders, $files);
		} else {
			$files = array_merge($files, $folders);
		}

		return $files;
	}

/**
 * ZipDownloaderを返す
 *
 * @return ZipDownloader ZipDownloader
 */
	protected function _getZipDownloader() {
		$zipDownloader = new ZipDownloader();
		return $zipDownloader;
	}
}
