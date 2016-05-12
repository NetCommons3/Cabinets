<?php
/**
 * CabinetFile Model
 *
 * @property CabinetCategory $CabinetCategory
 * @property CabinetFileTagLink $CabinetFileTagLink
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('CabinetsAppModel', 'Cabinets.Model');
App::uses('NetCommonsTime', 'NetCommons.Utility');
//App::uses('AttachmentBehavior', 'Files.Model/Behavior');

/**
 * Summary for CabinetFile Model
 */
class CabinetFile extends CabinetsAppModel {

/**
 * @var int recursiveはデフォルトアソシエーションなしに
 */
	public $recursive = 0;

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.Trackable',
		//'Tags.Tag',
		'NetCommons.OriginalKey',
		//'NetCommons.Publishable',
		'Workflow.Workflow',
		//'Likes.Like',
		'Workflow.WorkflowComment',
		//'Categories.Category',
		//'Cabinets.CabinetFileRename',
		'Files.Attachment' => [
			//'foo_photo' => [
			//		'thumbnailSizes' => array(
			//			// デフォルトはAttachmentビヘイビアできめてあるが、下記の様に設定も可能
			//			// NC2 800 > 640 > 480だった
			//				'big' => '800ml',
			//				'medium' => '400ml',
			//				'small' => '200ml',
			//				'thumb' => '80x80',
			//		),
			//	//'contentKeyFieldName' => 'id'
			//],
			'file' => [
				//'thumbnails' => false,
			]
		],
		'AuthorizationKeys.AuthorizationKey',
		'Topics.Topics' => array(
			'fields' => array(
				'title' => 'filename',
				'summary' => 'description',
				'path' => '/:plugin_key/cabinet_files/view/:block_id/:content_key',
			),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'CabinetFileTree' => array(
			'type' => 'LEFT',
			'className' => 'Cabinets.CabinetFileTree',
			'foreignKey' => false,
			//'conditions' => 'CabinetFileTree.cabinet_file_key=CabinetFile.key',
			'conditions' => 'CabinetFileTree.cabinet_file_id=CabinetFile.id',
			'fields' => '',
			'order' => ''
		),
		'Cabinet' => array(
			'type' => 'LEFT',
			'className' => 'Cabinets.Cabinet',
			'foreignKey' => false,
			'conditions' => 'CabinetFile.cabinet_id=Cabinet.id',
			'fields' => '',
			'order' => ''
		),
	);

/**
 * バリデーションルールを返す
 *
 * @return array
 */
	protected function _getValidateSpecification() {
		$validate = array(
			'pdf' => [
				'rule' => array('isValidExtension', array('pdf'), false),
				'message' => 'pdf only'
			],

			'filename' => array(
				'notBlank' => [
					'rule' => array('notBlank'),
					'message' => sprintf(
						__d('net_commons', 'Please input %s.'),
						__d('cabinets', 'Title')
					),
					//'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				],
			),
			'status' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'is_auto_translated' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
		);
		return $validate;
	}

/**
 * ルートフォルダを得る
 *
 * @param array $cabinet Cabinetデータ
 * @return array|null
 */
	public function getRootFolder($cabinet) {
		return $this->find('first', ['conditions' => $this->_getRootFolderConditions($cabinet)]);
	}

/**
 * キャビネットのルートフォルダとキャビネットの同期
 * ルートフォルダがなければ作成する
 *
 * @param array $cabinet Cabinet model data
 * @return bool
 * @throws Exception
 */
	public function syncRootFolder($cabinet) {
		if ($this->rootFolderExist($cabinet)) {
			// ファイル名同期
			$options = [
				'conditions' => $this->_getRootFolderConditions($cabinet)
			];
			$rootFolder = $this->find('first', $options);
			$rootFolder['CabinetFile']['filename'] = $cabinet['Cabinet']['name'];
			return ($this->save($rootFolder)) ? true : false;
		} else {
			return $this->makeRootFolder($cabinet);
		}
	}

/**
 * Cabinetのルートフォルダを作成する
 *
 * @param array $cabinet Cabinetモデルデータ
 * @return bool
 */
	public function makeRootFolder($cabinet) {
		if ($this->rootFolderExist($cabinet)) {
			return true;
		}
		//
		$this->create();
		$rootFolder = [
			'CabinetFile' => [
				'cabinet_id' => $cabinet['Cabinet']['id'],
				'status' => WorkflowComponent::STATUS_PUBLISHED,
				'filename' => $cabinet['Cabinet']['name'],
				'is_folder' => 1,
			]
		];

		if ($rootFolder = $this->save($rootFolder)) {
			$tree = [
				'CabinetFileTree' => [
					'cabinet_key' => $cabinet['Cabinet']['key'],
					'cabinet_file_key' => $rootFolder['CabinetFile']['key'],
					'cabinet_file_id' => $rootFolder['CabinetFile']['id'],
				]
			];
			$result = $this->CabinetFileTree->save($tree);
			return ($result) ? true : false;
		} else {
			return false;
		}
	}

/**
 * Cabinetのルートフォルダが存在するか
 *
 * @param array $cabinet Cabinetデータ
 * @return bool true:存在する false:存在しない
 */
	public function rootFolderExist($cabinet) {
		// ルートフォルダが既に存在するかを探す
		$conditions = $this->_getRootFolderConditions($cabinet);
		$count = $this->find('count', ['conditions' => $conditions]);
		return ($count > 0);
	}

/**
 * 空の新規データを返す
 *
 * @return array
 */
	public function getNew() {
		$new = parent::getNew();
		$netCommonsTime = new NetCommonsTime();
		$new['CabinetFile']['publish_start'] = $netCommonsTime->getNowDatetime();
		return $new;
	}

/**
 * UserIdと権限から参照可能なFileを取得するCondition配列を返す
 *
 * @param int $blockId ブロックId
 * @param int $userId アクセスユーザID
 * @param array $permissions 権限
 * @param datetime $currentDateTime 現在日時
 * @return array condition
 */
	public function getConditions($blockId, $userId, $permissions, $currentDateTime) {
		// contentReadable falseなら何も見えない
		if ($permissions['content_readable'] === false) {
			$conditions = array('CabinetFile.id' => 0); // ありえない条件でヒット0にしてる
			return $conditions;
		}

		// デフォルト絞り込み条件
		$conditions = array(
			'CabinetFile.block_id' => $blockId
		);

		$conditions = $this->getWorkflowConditions($conditions);

		//if ($permissions['content_editable']) {
		////if ($this->canEditW/orkflowContent()) {
		//	// 編集権限
		//	$conditions['CabinetFile.is_latest'] = 1;
		//	return $conditions;
		//}
		//
		//if ($permissions['content_creatable']) {
		////if ($this->canCreateWorkflowContent()) {
		//	// 作成権限
		//	$conditions['OR'] = array(
		//		array_merge(
		//			$this->_getPublishedConditions($currentDateTime),
		//			array('CabinetFile.created_user !=' => $userId)
		//		),
		//		array('CabinetFile.created_user' => $userId,
		//				'CabinetFile.is_latest' => 1)
		//	);
		//	return $conditions;
		//}
		//
		//if ($permissions['content_readable']) {
		////if ($this->canReadWorkflowContent()) {
		//	 //公開中コンテンツだけ
		//	$conditions = array_merge(
		//		$conditions,
		//		$this->_getPublishedConditions($currentDateTime));
		//	return $conditions;
		//}

		return $conditions;
	}

/**
 * 年月毎のファイル数を返す
 *
 * @param int $blockId ブロックID
 * @param int $userId ユーザID
 * @param array $permissions 権限
 * @param datetime $currentDateTime 現在日時
 * @return array
 */
	public function getYearMonthCount($blockId, $userId, $permissions, $currentDateTime) {
		$conditions = $this->getConditions($blockId, $userId, $permissions, $currentDateTime);
		// 年月でグループ化してカウント→取得できなかった年月をゼロセット
		$this->virtualFields['year_month'] = 0; // バーチャルフィールドを追加
		$this->virtualFields['count'] = 0; // バーチャルフィールドを追加
		$result = $this->find(
			'all',
			array(
				'fields' => array(
					'DATE_FORMAT(CabinetFile.publish_start, \'%Y-%m\') AS CabinetFile__year_month',
					'count(*) AS CabinetFile__count'
				),
				'conditions' => $conditions,
				'group' => array('CabinetFile__year_month'),
				//GROUP BY YEAR(record_date), MONTH(record_date)
			)
		);
		// 使ったバーチャルFieldを削除
		unset($this->virtualFields['year_month']);
		unset($this->virtualFields['count']);

		$ret = array();
		// $retをゼロ埋め
		//　一番古いファイルを取得
		$oldestFile = $this->find(
			'first',
			array(
				'conditions' => $conditions,
				'order' => 'publish_start ASC',
			)
		);

		// 一番古いファイルの年月から現在までを先にゼロ埋め
		if (isset($oldestFile['CabinetFile'])) {
			$currentYearMonthDay = date(
				'Y-m-01',
				strtotime($oldestFile['CabinetFile']['publish_start'])
			);
		} else {
			// ファイルがなかったら今月だけ
			$currentYearMonthDay = date('Y-m-01', strtotime($currentDateTime));
		}
		while ($currentYearMonthDay <= $currentDateTime) {
			$ret[substr($currentYearMonthDay, 0, 7)] = 0;
			$currentYearMonthDay = date('Y-m-01', strtotime($currentYearMonthDay . ' +1 month'));
		}
		// ファイルがある年月はファイル数を上書きしておく
		foreach ($result as $yearMonth) {
			$ret[$yearMonth['CabinetFile']['year_month']] = $yearMonth['CabinetFile']['count'];
		}

		//年月降順に並び替える
		krsort($ret);
		return $ret;
	}

/**
 * save ファイル
 *
 * @param array $data CabinetFileデータ
 * @return bool|mixed
 * @throws InternalErrorException
 */
	public function saveFile($data) {
		$this->begin();
		$this->_autoRename($data);
		try {
			$this->create(); // 常に新規登録
			$data['CabinetFile']['cabinet_file_tree_parent_id'] = $data['CabinetFileTree']['parent_id'];
			// 先にvalidate 失敗したらfalse返す
			$this->set($data);
			if (!$this->validates($data)) {
				$this->rollback();
				return false;
			}
			if (($savedData = $this->save($data, false)) === false) {
				//このsaveで失敗するならvalidate以外なので例外なげる
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			// TODO afterSaveへ
			if ($data['CabinetFile']['is_folder']) {
				// フォルダは treeをupdate
				//if(isset($data['CabinetFileTree']['id']) === false){
				//	$data['CabinetFileTree']['id'] = null;
				//}
			} else {
				// ファイルは treeを常にinsert
				$data['CabinetFileTree']['id'] = null;
			}
			$data['CabinetFileTree']['cabinet_file_key'] = $savedData[$this->alias]['key'];
			$data['CabinetFileTree']['cabinet_file_id'] = $savedData[$this->alias]['id'];
			//$count = $this->CabinetFileTree->find('count',['conditions' => ['cabinet_file_key' => $data[$this->alias]['key']]]);
			//if($count == 0){
			//	// 新規保存
			//	$this->CabinetFileTree->create();
			//	$data['CabinetFileTree']['cabinet_file_key'] = $savedData[$this->alias]['key'];
			//}

			// TODO treeはファイルなら常に新規INSERT フォルダだったらアップデート

			// TODO 例外処理
			// ここは単純マージじゃダメ
			$this->CabinetFileTree->create();
			$treeData = $this->CabinetFileTree->save($data);
			$savedData['CabinetFileTree'] = $treeData['CabinetFileTree'];

			$this->commit();
			return $savedData;

		} catch (Exception $e) {
			$this->rollback();
			//エラー出力
			CakeLog::error($e);
			throw $e;
		}
	}

/**
 * ファイル削除
 *
 * @param string $key CabinetFile.key
 * @return bool
 * @throws Exception
 * @throws null
 */
	public function deleteFileByKey($key) {
		$this->begin();
		try {
			$deleteFile = $this->findByKey($key);

			if ($deleteFile['CabinetFile']['is_folder']) {
				return $this->_deleteFolder($deleteFile);
			} else {
				return $this->_deleteFile($deleteFile);
			}
			$this->commit();
		} catch (Exception $e) {
			$this->rollback($e);
			throw $e;
		}
	}

/**
 * ファイル削除処理
 *
 * @param array $cabinetFile CabinetFile データ ファイル
 * @throws InternalErrorException
 * @return bool
 */
	protected function _deleteFile($cabinetFile) {
		//コメントの削除
		$this->deleteCommentsByContentKey($cabinetFile['CabinetFile']['key']);

		$conditions = array('CabinetFile.key' => $cabinetFile['CabinetFile']['key']);

		if ($result = $this->deleteAll($conditions, true, true)) {
			// CabinetFileTreeも削除
			$conditions = [
				'cabinet_file_key' => $cabinetFile['CabinetFile']['key'],
			];
			if (!$this->CabinetFileTree->deleteAll($conditions, true, true)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			return true;
		} else {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
	}

/**
 * フォルダ削除処理
 *
 * @param array $cabinetFile CabinetFileデータ フォルダ
 * @throws InternalErrorException
 * @return bool
 */
	protected function _deleteFolder($cabinetFile) {
		$key = $cabinetFile['CabinetFile']['key'];

		// 子ノードを全て取得
		$children = $this->CabinetFileTree->children(
			$cabinetFile['CabinetFileTree']['id'],
			false,
			null,
			null,
			null,
			1,
			0
		);
		if ($children) {
			foreach ($children as $child) {
				if ($child['CabinetFile']['is_folder']) {
					// folder delete
					$conditions = array('CabinetFile.key' => $child['CabinetFile']['key']);
					if (!$this->deleteAll($conditions)) {
						throw new InternalErrorException(
							__d('net_commons', 'Internal Server Error')
						);
					}
				} else {
					if ($child['CabinetFile']['is_latest']) {
						$conditions = array('CabinetFile.key' => $child['CabinetFile']['key']);
						if (!$this->deleteAll($conditions, true, true)) {
							throw new InternalErrorException(
								__d('net_commons', 'Internal Server Error')
							);
						}
					} else {
						// is_latestでなければ履歴データとしてCabinetFileは残してTreeだけ削除（ツリービヘイビアが勝手にけしてくれる）
					}
				}
			}
		}
		$conditions = array('CabinetFile.key' => $key);
		if ($result = $this->deleteAll($conditions, true, true)) {

			// CabinetFileTreeも削除 Treeビヘイビアにより子ノードのTreeデータは自動的に削除される
			$conditions = [
				'cabinet_file_key' => $key,
			];
			if (!$this->CabinetFileTree->deleteAll($conditions, true, true)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->commit();
			return true;
		} else {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
	}

/**
 * 過去に一度も公開されてないか
 *
 * @param array $cabinetFile チェック対象ファイル
 * @return bool true:公開されてない false: 公開されたことあり
 */
	public function yetPublish($cabinetFile) {
		$conditions = array(
			'CabinetFile.key' => $cabinetFile['CabinetFile']['key'],
			'CabinetFile.is_active' => 1
		);
		$count = $this->find('count', array('conditions' => $conditions));
		return ($count == 0);
	}

/**
 * フォルダの合計サイズを得る
 *
 * @param array $folder CabinetFileデータ
 * @return int 合計サイズ
 */
	public function getTotalSizeByFolder($folder) {
		// ベタパターン
		// 配下全てのファイルを取得する
		//$this->CabinetFileTree->setup(]);
		$cabinetKey = $folder['Cabinet']['key'];
		//$this->CabinetFileTree->Behaviors->unload('Tree');
		//$this->CabinetFileTree->Behaviors->load('Tree', ['scope' => ['CabinetFileTree.cabinet_key' => $cabinetKey]]);
		//$files = $this->CabinetFileTree->children($folder['CabinetFileTree']['id']);
		$conditions = [
			'CabinetFileTree.cabinet_key' => $cabinetKey,
			'CabinetFileTree.lft >' => $folder['CabinetFileTree']['lft'],
			'CabinetFileTree.rght <' => $folder['CabinetFileTree']['rght'],
			'CabinetFile.is_folder' => false,
		];
		$files = $this->find('all', ['conditions' => $conditions]);
		$total = 0;
		foreach ($files as $file) {

			$total += Hash::get($file, 'UploadFile.file.size', 0);
		}
		return $total;
		//$list = $this->CabinetFileTree->generateTreeList($conditions, 'cabinet_file_key');

		// 全てのファイルのサイズを集計する。

		// sumパターンはUploadFileの構造をしらないと厳しい… がんばってsumするより合計サイズをキャッシュした方がいいかも
	}

/**
 * 公開データ取得のconditionsを返す
 *
 * @param datetime $currentDateTime 現在の日時
 * @return array
 */
	protected function _getPublishedConditions($currentDateTime) {
		return array(
			$this->name . '.is_active' => 1,
			'CabinetFile.publish_start <=' => $currentDateTime,
		);
	}

/**
 * ルートフォルダ（＝キャビネット）をFindするためのconditionsを返す
 *
 * @param array $cabinet Cabinetデータ
 * @return array conditions
 */
	protected function _getRootFolderConditions($cabinet) {
		$conditions = [
			'cabinet_key' => $cabinet['Cabinet']['key'],
			'parent_id' => null,
		];
		return $conditions;
	}

/**
 * 親フォルダデータを返す
 *
 * @param array $cabinetFile cabinetFile data
 * @return array cabinetFile data
 */
	public function getParent($cabinetFile) {
		$conditions = [
			'CabinetFileTree.id' => $cabinetFile['CabinetFileTree']['parent_id'],
		];

		$parentCabinetFolder = $this->find('first', ['conditions' => $conditions]);
		return $parentCabinetFolder;
	}

/**
 * 子ノードがあるか
 *
 * @param array $cabinetFile cabinetFile(folder)data
 * @return bool true:あり
 */
	public function hasChildren($cabinetFile) {
		// 自分自身が親IDとして登録されてるデータがあれば子ノードあり
		$conditions = [
			'CabinetFileTree.parent_id' => $cabinetFile['CabinetFileTree']['id'],
		];
		$conditions = $this->getWorkflowConditions($conditions);
		$count = $this->find('count', ['conditions' => $conditions]);
		return ($count > 0);
	}

/**
 * キャビネットファイルのUnzip
 *
 * @param array $cabinetFile CabinetFileデータ
 * @return bool
 * @throws InternalErrorException
 */
	public function unzip($cabinetFile) {
		$this->begin();

		try {
			// テンポラリフォルダにunzip
			$zipPath = WWW_ROOT . $cabinetFile['UploadFile']['file']['path'] .
				$cabinetFile['UploadFile']['file']['id'] . DS .
				$cabinetFile['UploadFile']['file']['real_file_name'];
			//debug($zipPath);
			App::uses('UnZip', 'Files.Utility');
			$unzip = new UnZip($zipPath);
			$tmpFolder = $unzip->extract();
			if ($tmpFolder === false) {
				throw new InternalErrorException('UnZip Failed.');
			}
			// 再帰ループで登録処理
			$parentCabinetFolder = $this->find(
				'first',
				['conditions' => ['CabinetFileTree.id' => $cabinetFile['CabinetFileTree']['parent_id']]]
			);

			list($folders, $files) = $tmpFolder->read(true, false, true);
			foreach ($files as $file) {
				$this->_addFileFromPath($parentCabinetFolder, $file);
			}
			foreach ($folders as $folder) {
				$this->_addFolderFromPath($parentCabinetFolder, $folder);
			}
		} catch (Exception $e) {
			$this->rollback($e);
		}
		$this->commit();
		return true;
	}

/**
 * フォルダパスにある実フォルダをキャビネットに登録する
 *
 * @param array $parentCabinetFolder 登録する親フォルダ
 * @param string $folderPath 実フォルダのパス
 * @throws InternalErrorException
 * @return void
 */
	protected function _addFolderFromPath($parentCabinetFolder, $folderPath) {
		$newFolder = [
			'CabinetFile' => [
				'cabinet_id' => $parentCabinetFolder['CabinetFile']['cabinet_id'],
				'is_folder' => true,
				'filename' => $this->basename($folderPath),
				'status' => WorkflowComponent::STATUS_PUBLISHED,
			],
			'CabinetFileTree' => [
				'parent_id' => $parentCabinetFolder['CabinetFileTree']['id'],
				'cabinet_key' => $parentCabinetFolder['CabinetFileTree']['cabinet_key'],
			],
		];
		$newFolder = $this->create($newFolder);

		if (!$savedFolder = $this->saveFile($newFolder)) {
			throw new InternalErrorException('Save Failed');
		}
		//// folder配下のread
		$thisFolder = new Folder($folderPath);
		list($folders, $files) = $thisFolder->read(true, false, true);
		// 配下のファイル登録
		foreach ($files as $childFilePath) {
			$this->_addFileFromPath($savedFolder, $childFilePath);
		}
		// 配下のフォルダ登録
		foreach ($folders as $childFolderPath) {
			$this->_addFolderFromPath($savedFolder, $childFolderPath);
		}
	}

/**
 * ファイルパスにある実ファイルをキャビネットに登録する
 *
 * @param array $parentCabinetFolder 登録する親フォルダ
 * @param string $filePath 実ファイルのパス
 * @throws InternalErrorException
 * @return void
 */
	protected function _addFileFromPath($parentCabinetFolder, $filePath) {
		$newFile = [
			'CabinetFile' => [
				'cabinet_id' => $parentCabinetFolder['CabinetFile']['cabinet_id'],
				'is_folder' => false,
				'filename' => $this->basename($filePath),
				'status' => WorkflowComponent::STATUS_PUBLISHED,
			],
			'CabinetFileTree' => [
				'parent_id' => $parentCabinetFolder['CabinetFileTree']['id'],
				'cabinet_key' => $parentCabinetFolder['CabinetFileTree']['cabinet_key'],
			],
		];
		$newFile = $this->create($newFile);

		if (!$savedFile = $this->saveFile($newFile)) {
			throw new InternalErrorException('Save Failed');
		}
		$this->attachFile($savedFile, 'file', $filePath);
	}

/**
 * php builtinのbasenameがlocale依存なので自前で
 *
 * @param string $filePath ファイルパス
 * @return string basename
 */
	public function basename($filePath) {
		// Win pathを / 区切りに変換しちゃう
		$filePath = str_replace('\\', '/', $filePath);
		$separatedPath = explode('/', $filePath);
		// 最後を取り出す
		$basenaem = array_pop($separatedPath);
		return $basenaem;
	}

/**
 * 拡張子抜きのファイル名と拡張子にわける
 *
 * @param string $fileName ファイル名
 * @return array [ファイル名,拡張子]
 */
	public function splitFileName($fileName) {
		// .あるか
		if (strpos($fileName, '.')) {
			// .あり
			$splitFileName = explode('.', $fileName);
			$extension = array_pop($splitFileName); // 最後の.以降が拡張子
			$withOutExtFilename = implode('.', $splitFileName);
			$ret = [
				$withOutExtFilename,
				$extension
			];
		} else {
			// .なし
			$ret = [
				$fileName,
				null
			];
		}
		return $ret;
	}

/**
 * 同一フォルダに同じ名前のファイル・フォルダがあるか
 *
 * @param array $cabinetFile CabinetFile データ
 * @return bool
 */
	protected function _existSameFilename($cabinetFile) {
		$conditions = [
			'CabinetFile.key !=' => $cabinetFile['CabinetFile']['key'],
			'CabinetFileTree.parent_id' => $cabinetFile['CabinetFileTree']['parent_id'],
			'CabinetFile.filename' => $cabinetFile['CabinetFile']['filename'],
		];
		$count = $this->find('count', ['conditions' => $conditions]);
		return ($count > 0);
	}

/**
 * 自動リネーム
 *
 * 同一フォルダ内で名前が衝突したら自動でリネームする
 *
 * @param array $cabinetFile CabinetFile データ
 * @return void
 */
	protected function _autoRename($cabinetFile) {
		$index = 0;
		if ($cabinetFile['CabinetFile']['is_folder']) {
			// folder
			$baseFolderName = $cabinetFile['CabinetFile']['filename'];
			while ($this->_existSameFilename($cabinetFile)) {
				// 重複し続ける限り数字を増やす
				$index++;
				$newFilename = sprintf('%s%03d', $baseFolderName, $index);
				$cabinetFile['CabinetFile']['filename'] = $newFilename;
			}
			$this->data['CabinetFile']['filename'] = $cabinetFile['CabinetFile']['filename'];
		} else {
			list($baseFileName, $ext) = $this->splitFileName(
				$cabinetFile['CabinetFile']['filename']
			);
			$extString = is_null($ext) ? '' : '.' . $ext;

			while ($this->_existSameFilename($cabinetFile)) {
				// 重複し続ける限り数字を増やす
				$index++;
				$newFilename = sprintf('%s%03d', $baseFileName, $index);
				$cabinetFile['CabinetFile']['filename'] = $newFilename . $extString;
			}
			$this->data['CabinetFile']['filename'] = $cabinetFile['CabinetFile']['filename'];
		}
	}

}
