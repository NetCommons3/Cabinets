<?php
/**
 * CabinetFile Model
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('CabinetsAppModel', 'Cabinets.Model');
App::uses('NetCommonsTime', 'NetCommons.Utility');

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
		'Cabinets.CabinetFolder',
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
		'Mails.MailQueue' => array(        // 自動でメールキューの登録, 削除。ワークフロー利用時はWorkflow.Workflowより下に記述する
			'embedTags' => array(
				'X-SUBJECT' => 'CabinetFile.filename',
				'X-BODY' => 'CabinetFile.description',
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
 * beforeValidate
 *
 * @param array $options Options
 * @return bool
 */
	public function beforeValidate($options = array()) {
		$validate = array(
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

		$this->validate = Hash::merge($this->validate, $validate);

		return parent::beforeValidate($options);
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
			$this->rollback($e);
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

		if ($this->deleteAll($conditions, true, true)) {
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
		if ($this->deleteAll($conditions, true, true)) {

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

			$parentCabinetFolder = $this->find(
				'first',
				['conditions' => ['CabinetFileTree.id' => $cabinetFile['CabinetFileTree']['parent_id']]]
			);

			// unzipされたファイル拡張子のバリデーション
			// unzipされたファイルのファイルサイズバリデーション
			$files = $tmpFolder->findRecursive();
			$unzipTotalSize = 0;
			foreach ($files as $file) {
				//
				$unzipTotalSize += filesize($file);

				// ここでは拡張子だけチェックする
				$extension = pathinfo($file, PATHINFO_EXTENSION);
				if (!$this->isAllowUploadFileExtension($extension)) {
					// NG
					$this->validationErrors = [
						__d('cabinets', 'Unzip failed. Contains does not allow file format.')
					];
					return false;
				}
			}
			// ルームファイルサイズ制限
			//$maxRoomDiskSize = Current::read('Space.room_disk_size');
			//if ($maxRoomDiskSize !== null) {
			//	// nullだったらディスクサイズ制限なし。null以外ならディスクサイズ制限あり
			//	// 解凍後の合計
			//	// 現在のルームファイルサイズ
			//	$uploadFile = ClassRegistry::init('Files.UploadFile');
			//	$roomId = Current::read('Room.id');
			//	$roomFileSize = $uploadFile->getTotalSizeByRoomId($roomId);
			//	if (($roomFileSize + $unzipTotalSize) > $maxRoomDiskSize) {
			//
			//		$this->validationErrors[] = __d(
			//			'cabinets',
			//			'Failed to expand. The total size exceeds the limit.<br />The total size limit is %s (%s left).',
			//			CakeNumber::toReadableSize($roomFileSize + $unzipTotalSize),
			//			CakeNumber::toReadableSize($maxRoomDiskSize)
			//		);
			//		return false;
			//	}
			//}

			// 再帰ループで登録処理
			list($folders, $files) = $tmpFolder->read(true, false, true);
			foreach ($files as $file) {
				$this->_addFileFromPath($parentCabinetFolder, $file);
			}
			foreach ($folders as $folder) {
				$this->_addFolderFromPath($parentCabinetFolder, $folder);
			}
		} catch (Exception $e) {
			return $this->rollback($e);
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
		$newFile = $this->_makeCabinetFileDataFromPath($parentCabinetFolder, $filePath);

		if (!$this->saveFile($newFile)) {
			throw new InternalErrorException('Save Failed');
		}
		//$this->attachFile($savedFile, 'file', $filePath);
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
		$conditions = $this->getWorkflowConditions($conditions);
		$count = $this->find('count', ['conditions' => $conditions]);
		return ($count > 0);
	}

/**
 * 自動リネーム
 *
 * 同一フォルダ内で名前が衝突したら自動でリネームする
 *
 * @param array &$cabinetFile CabinetFile データ
 * @return void
 */
	protected function _autoRename(& $cabinetFile) {
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

	/**
	 * CabinetFileデータをファイルパスから作成する
	 *
	 * @param array $parentCabinetFolder 親フォルダデータ
	 * @param string $filePath ファイルパス
	 * @return array フォームからポストされる形のCabinetFileデータ
	 */
	protected function _makeCabinetFileDataFromPath($parentCabinetFolder, $filePath) {
		//MIMEタイプの取得
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$mimeType = $finfo->file($filePath);

		$newFile = [
			'CabinetFile' => [
				'cabinet_id' => $parentCabinetFolder['CabinetFile']['cabinet_id'],
				'is_folder' => false,
				'filename' => $this->basename($filePath),
				'status' => WorkflowComponent::STATUS_PUBLISHED,
				'file' => [
					'name' => $this->basename($filePath),
					'type' => $mimeType,
					'tmp_name' => $filePath,
					'error' => 0,
					'size' => filesize($filePath),
				],
			],
			'CabinetFileTree' => [
				'parent_id' => $parentCabinetFolder['CabinetFileTree']['id'],
				'cabinet_key' => $parentCabinetFolder['CabinetFileTree']['cabinet_key'],
			],
		];
		$newFile = $this->create($newFile);
		return $newFile;
	}
}