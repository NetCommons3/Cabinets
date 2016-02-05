<?php
/**
 * CabinetEtnriesController
 */
App::uses('CabinetsAppController', 'Cabinets.Controller');

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
 * @property CabinetCategory $CabinetCategory
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
		'ContentComments.ContentComment',	// コンテンツコメント
	);

/**
 * @var array helpers
 * @var array helpers
 */
	public $helpers = array(
		'NetCommons.Token',
		'NetCommons.BackTo',
		'Workflow.Workflow',
		'Likes.Like',
		'Users.DisplayUser'
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
		'ContentComments.ContentComments',
		'Files.Download',
		'AuthorizationKeys.AuthorizationKey' => [
			//'operationType' => AuthorizationKeyComponent::OPERATION_REDIRECT,
			'operationType' => 'popup',
			//'operationType' => 'redirect',
			'targetAction' => 'download_pdf',
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
		$this->Auth->allow('index', 'view', 'category', 'tag', 'year_month', 'download', 'download_pdf');
		//$this->Categories->initCategories();
		//$this->AuthorizationKey->contentId =23; // TODO hardcord
		//$this->AuthorizationKey->model ='CabinetFile'; // TODO hardcord
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
		if (! Current::read('Block.id')) {
			$this->autoRender = false;
			return;
		}


		$this->CabinetFileTree->recover('parent');

		// 全フォルダツリーを得る
		$conditions = [
			'is_folder' => 1,
		];
		$folders = $this->CabinetFileTree->find('threaded', ['conditions' => $conditions, 'recursive' => 0, 'order' => 'CabinetFile.filename ASC']);
		$this->set('folders', $folders);



		// カレントフォルダのファイル・フォルダリストを得る。
		$folderKey = isset($this->request->params['pass'][1]) ? $this->request->params['pass'][1] : null;
		if (is_null($folderKey)){
			$currentTreeId = null;
		}else{
			$currentFolder = $this->CabinetFileTree->find('first', ['conditions' => ['cabinet_file_key' => $folderKey]]);
			$currentTreeId = $currentFolder['CabinetFileTree']['id'];
		}

		$this->set('currentTreeId', $currentTreeId);
		$conditions = [
			'parent_id' => $currentTreeId
		];
		//  workflowコンディションを混ぜ込む
		$conditions = $this->CabinetFile->getWorkflowConditions($conditions);
		// TODO ソート順変更
		$files = $this->CabinetFile->find('all', ['conditions' => $conditions, 'order' => 'filename ASC']);
		$this->set('cabinetFiles', $files);

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

		$this->set('listTitle', $this->_cabinetTitle);

		return;

	}


	public function folder_detail() {
		// TODO folderじゃなかったらエラー
		$folderKey = isset($this->request->params['pass'][1]) ? $this->request->params['pass'][1] : null;
		$conditions = [
			'CabinetFile.key' => $folderKey,
			'CabinetFile.cabinet_id' => $this->_cabinet['Cabinet']['id']
		];
		$conditions = $this->CabinetFile->getWorkflowConditions($conditions);
		$cabinetFile = $this->CabinetFile->find('first', ['conditions' => $conditions]);
		$this->set('cabinetFile', $cabinetFile);

		$this->_setFolderPath($cabinetFile);
	}

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
 * 一覧
 *
 * @param array $extraConditions 追加conditions
 * @return void
 */
	protected function _list($extraConditions = array()) {

		//$this->_setYearMonthOptions();

		$permission = $this->_getPermission();

		$conditions = $this->CabinetFile->getConditions(
			Current::read('Block.id'),
			$this->Auth->user('id'),
			$permission,
			$this->_getCurrentDateTime()
		);
		if ($extraConditions) {
			$conditions = Hash::merge($conditions, $extraConditions);
		}
		$this->Paginator->settings = array_merge(
			$this->Paginator->settings,
			array(
				'conditions' => $conditions,
				'limit' => $this->_frameSetting['CabinetFrameSetting']['articles_per_page'],
				'order' => 'filename ASC',
			)
		);
		$this->CabinetFile->recursive = 0;
		$this->set('cabinetFiles', $this->Paginator->paginate());

		$this->render('index');
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @return void
 */
	public function view() {
		$this->_prepare();

		//$key = $this->request->params['named']['key'];
		$key = $this->params['pass'][1];

		$conditions = $this->CabinetFile->getConditions(
			Current::read('Block.id'),
			$this->Auth->user('id'),
			$this->_getPermission(),
			$this->_getCurrentDateTime()
		);

		$conditions['CabinetFile.key'] = $key;

		$options = array(
			'conditions' => $conditions,
			'recursive' => 0,
			'fields' => array(
				'*',
				'ContentCommentCnt.cnt',
			)
		);
		$this->CabinetFile->Behaviors->load('ContentComments.ContentComment');
		$cabinetFile = $this->CabinetFile->find('first', $options);
		$this->CabinetFile->Behaviors->unload('ContentComments.ContentComment');
		if ($cabinetFile) {
			$this->set('cabinetFile', $cabinetFile);
			// tag取得
			//$cabinetTags = $this->CabinetTag->getTagsByFileId($cabinetFile['CabinetFile']['id']);
			//$this->set('cabinetTags', $cabinetTags);

			// コメントを利用する
			if ($this->_cabinetSetting['CabinetSetting']['use_comment']) {
				if ($this->request->isPost()) {
					// コメントする
					if (!$this->ContentComments->comment('cabinets', $cabinetFile['CabinetFile']['key'], $this->_cabinetSetting['CabinetSetting']['use_comment_approval'])) {
						$this->throwBadRequest();
						return;
					}
				}

				// コンテンツコメントの取得
				$contentComments = $this->ContentComment->getContentComments(array(
					//'block_key' => $this->viewVars['blockKey'],
					'block_key' => Current::read('Block.key'),
					'plugin_key' => 'cabinets',
					'content_key' => $cabinetFile['CabinetFile']['key'],
				));
				$contentComments = $this->camelizeKeyRecursive($contentComments);
				$this->set('contentComments', $contentComments);
			}

		} else {
			// 表示できないファイルへのアクセスなら404
			throw new NotFoundException(__('Invalid cabinet file'));
		}
	}

	public function download() {
		// ここから元コンテンツを取得する処理
		$this->_prepare();
		$key = $this->params['pass'][1];
		$conditions = $this->CabinetFile->getConditions(
			Current::read('Block.id'),
			$this->Auth->user('id'),
			$this->_getPermission(),
			$this->_getCurrentDateTime()
		);

		$conditions['CabinetFile.key'] = $key;
		$options = array(
			'conditions' => $conditions,
		);
		$cabinetFile = $this->CabinetFile->find('first', $options);
		// ここまで元コンテンツを取得する処理

		// ダウンロード実行
		if ($cabinetFile) {
			return $this->Download->doDownload($cabinetFile['CabinetFile']['id']);
		} else {
			// 表示できないファイルへのアクセスなら404
			throw new NotFoundException(__('Invalid cabinet file'));
		}
	}

	public function download_pdf() {
		// ここから元コンテンツを取得する処理
		$this->_prepare();
		$key = $this->params['pass'][1];

		$conditions = $this->CabinetFile->getConditions(
				Current::read('Block.id'),
				$this->Auth->user('id'),
				$this->_getPermission(),
				$this->_getCurrentDateTime()
		);

		$conditions['CabinetFile.key'] = $key;
		$options = array(
				'conditions' => $conditions,
				'recursive' => 1,
		);
		$cabinetFile = $this->CabinetFile->find('first', $options);
		// ここまで元コンテンツを取得する処理

		$this->AuthorizationKey->guard('popup', 'CabinetFile', $cabinetFile);

		// ダウンロード実行
		if ($cabinetFile) {
			return $this->Download->doDownload($cabinetFile['CabinetFile']['id'], ['filed' => 'pdf']);
		} else {
			// 表示できないファイルへのアクセスなら404
			throw new NotFoundException(__('Invalid cabinet file'));
		}
	}

}
