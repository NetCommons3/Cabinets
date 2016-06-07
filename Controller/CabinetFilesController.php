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
		'Categories.Category',
	);

/**
 * @var array helpers
 * @var array helpers
 */
	public $helpers = array(
		'NetCommons.Token',
		'NetCommons.BackTo',
		'Workflow.Workflow',
		'Users.DisplayUser',
		'Paginator'
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
		'Categories.Categories',
		'Files.Download',
		'AuthorizationKeys.AuthorizationKey' => [
			//'operationType' => AuthorizationKeyComponent::OPERATION_REDIRECT,
			'operationType' => 'redirect',
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
			'category',
			'tag',
			'year_month',
			'download',
			'download_pdf'
		);
		parent::beforeFilter();
		$blockId = Current::read('Block.id');
		$this->_cabinet = $this->Cabinet->findByBlockId($blockId);
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
		$this->CabinetFileTree->recover('parent');

		// currentFolderを取得
		$folderKey = isset($this->request->params['pass'][1]) ? $this->request->params['pass'][1] : null;
		if (is_null($folderKey)) {
			// 指定がなければルートフォルダを取得する
			$currentFolder = $this->CabinetFile->getRootFolder($this->_cabinet);
			$currentTreeId = $currentFolder['CabinetFileTree']['id'];
		} else {
			$currentFolder = $this->CabinetFile->find(
				'first',
				['conditions' => ['CabinetFile.key' => $folderKey]]
			);
			$currentTreeId = $currentFolder['CabinetFileTree']['id'];
		}

		$this->set('currentFolder', $currentFolder);
		$this->set('currentTreeId', $currentTreeId);

		// 全フォルダツリーを得る
		$conditions = [
			'is_folder' => 1,
			'cabinet_key' => $this->viewVars['cabinet']['Cabinet']['key']
		];
		$folders = $this->CabinetFileTree->find(
			'threaded',
			['conditions' => $conditions, 'recursive' => 0, 'order' => 'CabinetFile.filename ASC']
		);
		$this->set('folders', $folders);

		// カレントフォルダのファイル・フォルダリストを得る。
		$conditions = [
			'parent_id' => $currentTreeId,
			'cabinet_id' => $this->viewVars['cabinet']['Cabinet']['id']
		];
		//  workflowコンディションを混ぜ込む
		$conditions = $this->CabinetFile->getWorkflowConditions($conditions);
		// ソート順変更
		// ソートに使えるキーかチェック
		$allowSortKeys = [
			'filename',
			'size',
			'modified'
		];

		$sort = Hash::get($this->request->named, 'sort', 'filename');
		if (!in_array($sort, $allowSortKeys)) {
			return $this->throwBadRequest();
		}
		//  asc, descしか入力を許可しない
		$direction = Hash::get($this->request->named, 'direction', 'asc');
		if (!in_array($direction, ['asc', 'desc'])) {
			return $this->throwBadRequest();
		}
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
		//
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

		$this->set('cabinetFiles', $files);

		// カレントフォルダのツリーパスを得る
		$folderPath = $this->CabinetFileTree->getPath($currentTreeId, null, 0);
		$this->set('folderPath', $folderPath);

		// 1つ上へのリンク
		if ($currentFolder['CabinetFileTree']['parent_id'] && $currentFolder['CabinetFileTree']['parent_id'] != $folderPath[0]['CabinetFileTree']['id']) {
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
			$url = NetCommonsUrl::backToPageUrl();

		}
		if ($currentFolder['CabinetFileTree']['parent_id'] == null) {
			// root folder
			$url = null;
		}
		$this->set('parentUrl', $url);

		$this->set('listTitle', $this->_cabinetTitle);
	}

/**
 * フォルダ詳細
 *
 * @return void
 */
	public function folder_detail() {
		// TODO folderじゃなかったらエラー
		$folderKey = Hash::get($this->request->params, 'pass.1', null);

		$conditions = [
			'CabinetFile.key' => $folderKey,
			'CabinetFile.cabinet_id' => $this->_cabinet['Cabinet']['id']
		];
		$conditions = $this->CabinetFile->getWorkflowConditions($conditions);
		$cabinetFile = $this->CabinetFile->find('first', ['conditions' => $conditions]);

		$cabinetFile['CabinetFile']['size'] = $this->CabinetFile->getTotalSizeByFolder(
			$cabinetFile
		);
		//$cabinetFile['CabinetFile']['size'] =

		$this->set('cabinetFile', $cabinetFile);

		$this->_setFolderPath($cabinetFile);
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
		// TODO ファイルでなければエラー
		$folderKey = isset($this->request->params['pass'][1]) ? $this->request->params['pass'][1] : null;
		$conditions = [
			'CabinetFile.key' => $folderKey,
			'CabinetFile.cabinet_id' => $this->_cabinet['Cabinet']['id']
		];
		$conditions = $this->CabinetFile->getWorkflowConditions($conditions);
		$cabinetFile = $this->CabinetFile->find('first', ['conditions' => $conditions]);
		$this->set('cabinetFile', $cabinetFile);

		$this->_setFolderPath($cabinetFile);

		//新着データを既読にする
		$this->CabinetFile->saveTopicUserStatus($cabinetFile);
	}

/**
 * ファイルダウンロード
 *
 * @throws NotFoundException
 * @return mixed
 */
	public function download() {
		// ここから元コンテンツを取得する処理
		$folderKey = isset($this->request->params['pass'][1]) ? $this->request->params['pass'][1] : null;
		$conditions = [
			'CabinetFile.key' => $folderKey,
			'CabinetFile.cabinet_id' => $this->_cabinet['Cabinet']['id']
		];
		$conditions = $this->CabinetFile->getWorkflowConditions($conditions);
		$cabinetFile = $this->CabinetFile->find('first', ['conditions' => $conditions]);
		//$this->set('cabinetFile', $cabinetFile);
		// ここまで元コンテンツを取得する処理

		$this->AuthorizationKey->guard('redirect', 'CabinetFile', $cabinetFile);

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
			throw new NotFoundException(__('Invalid cabinet file'));
		}
	}

/**
 * フォルダのZIPダウンロード
 *
 * @return CakeResponse|string|void
 */
	public function download_folder() {
		// フォルダを取得
		$folderKey = isset($this->request->params['pass'][1]) ? $this->request->params['pass'][1] : null;
		$conditions = [
			'CabinetFile.key' => $folderKey,
			'CabinetFile.cabinet_id' => $this->_cabinet['Cabinet']['id']
		];
		$conditions = $this->CabinetFile->getWorkflowConditions($conditions);
		$cabinetFolder = $this->CabinetFile->find('first', ['conditions' => $conditions]);

		$tmpFolder = new TemporaryFolder();
		try {
			$this->_prepareDownload($tmpFolder->path, $cabinetFolder);
		} catch (Exception $e) {
			$this->set('error', $e->getMessage());

			return;
		}
		$zipDownloader = new ZipDownloader();

		list($folders, $files) = $tmpFolder->read(true, false, true);
		foreach ($folders as $folder) {
			$zipDownloader->addFolder($folder);
		}
		foreach ($files as $file) {
			$zipDownloader->addFile($file);
		}

		//$zipDownloader->addFolder($tmpFolder->path);

		return $zipDownloader->download($cabinetFolder['CabinetFile']['filename'] . '.zip');
	}

/**
 * フォルダのZIPダウンロード前処理
 *
 * @param string $path ダウンロード処理用テンポラリフォルダのカレントパス
 * @param array $cabinetFolder CabinetFileデータ 処理するフォルダ
 * @throws Exception
 * @return void
 */
	protected function _prepareDownload($path, $cabinetFolder) {
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
		foreach ($files as $file) {
			if ($file['CabinetFile']['is_folder']) {
				mkdir($path . DS . $file['CabinetFile']['filename']);
				$this->_prepareDownload($path . DS . $file['CabinetFile']['filename'], $file);
			} else {
				if (isset($file['AuthorizationKey'])) {
					throw new Exception(
						__d(
							'cabinets',
							'Folder that contains the files that are password is set can not be downloaded ZIP.'
						)
					);
				}
				// ダウンロードカウントアップ
				$this->CabinetFile->downloadCountUp($file, 'file');

				$filePath = WWW_ROOT . $file['UploadFile']['file']['path'] . $file['UploadFile']['file']['id'] . DS . $file['UploadFile']['file']['real_file_name'];
				//copy($filePath, $path . DS . $file['UploadFile']['file']['original_name']);
				copy($filePath, $path . DS . $file['CabinetFile']['filename']);

			}
		}
	}

}
