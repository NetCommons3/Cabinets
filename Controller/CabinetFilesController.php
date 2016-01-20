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

		$this->_prepare();
		$this->set('listTitle', $this->_cabinetTitle);
		$this->set('filterDropDownLabel', __d('cabinets', 'All Files'));

		$conditions = array();
		$this->_filter['categoryId'] = $this->_getNamed('category_id', 0);
		if ($this->_filter['categoryId']) {
			$conditions['CabinetFile.category_id'] = $this->_filter['categoryId'];
			$category = $this->Category->findById($this->_filter['categoryId']);
			$this->set('listTitle', __d('cabinets', 'Category') . ':' . $category['Category']['name']);
			$this->set('filterDropDownLabel', $category['Category']['name']);
		}

		$this->_list($conditions);
	}

/**
 * tag別一覧
 *
 * @return void
 */
	public function tag() {
		$this->_prepare();
		// indexとのちがいはtagIdでの絞り込みだけ
		$tagId = $this->_getNamed('id', 0);

		// カテゴリ名をタイトルに
		$tag = $this->CabinetFile->getTagByTagId($tagId);
		$this->set('listTitle', __d('cabinets', 'Tag') . ':' . $tag['Tag']['name']);
		$this->set('filterDropDownLabel', '----');

		$conditions = array(
			'Tag.id' => $tagId // これを有効にするにはfile_tag_linkもJOINして検索か。
		);

		$this->_list($conditions);
	}

/**
 * 年月別一覧
 *
 * @return void
 */
	public function year_month() {
		$this->_prepare();
		// indexとの違いはyear_monthでの絞り込み
		$this->_filter['yearMonth'] = $this->_getNamed('year_month', 0);

		list($year, $month) = explode('-', $this->_filter['yearMonth']);
		$this->set('listTitle', __d('cabinets', '%d-%d Cabinet File List', $year, $month));
		$this->set('filterDropDownLabel', __d('cabinets', '%d-%d', $year, $month));

		$first = $this->_filter['yearMonth'] . '-1';
		$last = date('Y-m-t', strtotime($first));

		$conditions = array(
			'CabinetFile.publish_start BETWEEN ? AND ?' => array($first, $last)
		);
		$this->_list($conditions);
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
		$this->set('currentCategoryId', $this->_filter['categoryId']);

		$this->set('currentYearMonth', $this->_filter['yearMonth']);

		$this->_setYearMonthOptions();

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
				'order' => 'publish_start DESC',
				'fields' => '*, ContentCommentCnt.cnt',
			)
		);
		$this->CabinetFile->recursive = 0;
		$this->CabinetFile->Behaviors->load('ContentComments.ContentComment');
		$this->set('cabinetFiles', $this->Paginator->paginate());
		$this->CabinetFile->Behaviors->unload('ContentComments.ContentComment');

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

	/**
 * 年月選択肢をViewへセット
 *
 * @return void
 */
	protected function _setYearMonthOptions() {
		// 年月とファイル数
		$yearMonthCount = $this->CabinetFile->getYearMonthCount(
			Current::read('Block.id'),
			$this->Auth->user('id'),
			$this->_getPermission(),
			$this->_getCurrentDateTime()
		);
		foreach ($yearMonthCount as $yearMonth => $count) {
			list($year, $month) = explode('-', $yearMonth);
			$options[$yearMonth] = __d('cabinets', '%d-%d (%s)', $year, $month, $count);
		}
		$this->set('yearMonthOptions', $options);
	}
}
