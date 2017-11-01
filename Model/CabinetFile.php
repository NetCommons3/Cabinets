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
App::uses('Current', 'NetCommons.Utility');

/**
 * Summary for CabinetFile Model
 */
class CabinetFile extends CabinetsAppModel {

/**
 * @var int recursiveはデフォルトアソシエーションなしに
 */
	public $recursive = 0;

/**
 * RootFolder作成時はfalseにセットしてfilenameを自由につけられるようにする
 *
 * @var bool
 */
	public $useNameValidation = true;

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.Trackable',
		'NetCommons.OriginalKey',
		'Workflow.Workflow',
		'Workflow.WorkflowComment',
		'Cabinets.CabinetFile',
		'Cabinets.CabinetFolder',
		'Cabinets.CabinetUnzip',
		'Files.Attachment' => [
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
		// 自動でメールキューの登録, 削除。ワークフロー利用時はWorkflow.Workflowより下に記述する
		'Mails.MailQueue' => array(
			'embedTags' => array(
				'X-SUBJECT' => 'CabinetFile.filename',
				'X-BODY' => 'CabinetFile.description',
			),
		),
		//多言語
		'M17n.M17n' => array(
			'commonFields' => array(
				'cabinet_file_tree_parent_id',
				'cabinet_file_tree_id',
				'is_folder',
				'use_auth_key',
			),
			'associations' => array(
				'UploadFilesContent' => array(
					'class' => 'Files.UploadFilesContent',
					'foreignKey' => 'content_id',
					'isM17n' => true
				),
				'AuthorizationKey' => array(
					'class' => 'AuthorizationKeys.AuthorizationKey',
					'foreignKey' => 'content_id',
					'fieldForIdentifyPlugin' => array('field' => 'model', 'value' => 'CabinetFile'),
					'isM17n' => false
				),
			),
			'afterCallback' => false,
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
			'foreignKey' => 'cabinet_file_tree_id',
			//'conditions' => 'CabinetFileTree.cabinet_file_key=CabinetFile.key',
			//'conditions' => 'CabinetFileTree.cabinet_file_id=CabinetFile.id',
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
						__d('cabinets', 'Filename')
					),
					//'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				],
			),
			'withOutExtFileName' => [
				'rule' => ['validateWithOutExtFileName'],
				'message' => sprintf(
					__d('net_commons', 'Please input %s.'),
					__d('cabinets', 'Filename')
				),
			],
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
		if ($this->useNameValidation) {
			$validate['filename']['filename'] = [
				'rule' => ['validateFilename'],
				'message' => __d('cabinets', 'Invalid character for file/folder name.'),
			];
		}

		$this->validate = Hash::merge($this->validate, $validate);

		return parent::beforeValidate($options);
	}

/**
 * Called before each find operation. Return false if you want to halt the find
 * call, otherwise return the (modified) query data.
 *
 * @param array $query Data used to execute this query, i.e. conditions, order, etc.
 * @return mixed true if the operation should continue, false if it should abort; or, modified
 *  $query to continue with new $query
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforefind
 */
	public function beforeFind($query) {
		if (Hash::get($query, 'recursive', $this->recursive) > -1) {
			$belongsTo = array(
				'belongsTo' => array(
					'Cabinet' => array(
						'className' => 'Cabinets.Cabinet',
						'foreignKey' => false,
						'conditions' => array(
							'CabinetFile.cabinet_key = Cabinet.key',
							'OR' => array(
								'Cabinet.is_translation' => false,
								'Cabinet.language_id' => Current::read('Language.id', '0'),
							),
						),
						'order' => ''
					),
				)
			);

			$this->bindModel($belongsTo, true);
		}
		return true;
	}

/**
 * Called before each save operation, after validation. Return a non-true result
 * to halt the save.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 * @throws InternalErrorException
 */
	public function beforeSave($options = array()) {
		if (isset($this->data['CabinetFileTree'])) {
			// treeはファイルなら常に新規INSERT フォルダだったらアップデート
			if ($this->data['CabinetFile']['is_folder']) {
				// フォルダは treeをupdate
				//if(isset($data['CabinetFileTree']['id']) === false){
				//	$data['CabinetFileTree']['id'] = null;
				//}
			} else {
				// ファイルは treeを常にinsert
				$this->data['CabinetFileTree']['id'] = null;
			}

			$this->CabinetFileTree->create();
			$treeData = $this->CabinetFileTree->save($this->data['CabinetFileTree']);
			if (! $treeData) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->data['CabinetFileTree'] = $treeData['CabinetFileTree'];
			$this->data[$this->alias]['cabinet_file_tree_id'] = $this->data['CabinetFileTree']['id'];
		}

		return parent::beforeSave($options);
	}

/**
 * Called after each successful save operation.
 *
 * @param bool $created True if this save created a new record
 * @param array $options Options passed from Model::save().
 * @return void
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#aftersave
 * @see Model::save()
 * @throws InternalErrorException
 */
	public function afterSave($created, $options = array()) {
		if (isset($this->data['CabinetFileTree'])) {
			$update = array(
				'CabinetFileTree.cabinet_file_key' => '\'' . $this->data[$this->alias]['key'] . '\'',
			);
			$conditions = array(
				'CabinetFileTree.id' => $this->data['CabinetFileTree']['id']
			);
			if (! $this->CabinetFileTree->updateAll($update, $conditions)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->data['CabinetFileTree']['cabinet_file_key'] = $this->data[$this->alias]['key'];
		}

		parent::afterSave($created, $options);
	}

/**
 * modifiedを常に更新
 *
 * @param null $data 登録データ
 * @param bool $validate バリデートを実行するか
 * @param array $fieldList フィールド
 * @return mixed
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function save($data = null, $validate = true, $fieldList = array()) {
		// 保存前に modified フィールドをクリアする
		$this->set($data);
		$isNoUnsetModified = Hash::get($this->data, $this->alias . '._is_no_unset_modified');
		if (isset($this->data[$this->alias]['modified']) && !$isNoUnsetModified) {
			unset($this->data[$this->alias]['modified']);
		}
		return parent::save($this->data, $validate, $fieldList);
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
			// 常に新規登録
			$this->create();
			unset($data[$this->alias]['id']);

			$data['CabinetFile']['cabinet_file_tree_parent_id'] = $data['CabinetFileTree']['parent_id'];

			// 先にvalidate 失敗したらfalse返す
			$this->set($data);
			if (!$this->validates($data)) {
				$this->rollback();
				return false;
			}
			if ($data['CabinetFile']['is_folder']) {
				// Folderは新着にのせたくないのでTopicディセーブル
				$this->Behaviors->disable('Topics');
			}
			if (($savedData = $this->save($data, false)) === false) {
				//このsaveで失敗するならvalidate以外なので例外なげる
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->Behaviors->enable('Topics');

			// Cabinet.total_size同期
			$this->updateCabinetTotalSize($data['CabinetFile']['cabinet_key']);

			//多言語化の処理
			$this->set($savedData);
			$this->saveM17nData();

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
			$deleteFile = $this->find('first', array(
				'recursive' => 0,
				'conditions' => array(
					'CabinetFile.key' => $key
				)
			));

			if ($deleteFile['CabinetFile']['is_folder']) {
				$this->_deleteFolder($deleteFile);
			} else {
				$this->_deleteFile($deleteFile);
			}
			$this->updateCabinetTotalSize($deleteFile['CabinetFile']['cabinet_key']);

			$this->commit();
			return;
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

		// CabinetFileTreeも削除
		$conditions = [
			'cabinet_file_key' => $cabinetFile['CabinetFile']['key'],
		];
		if (! $this->CabinetFileTree->deleteAll($conditions, true, true)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

		$conditions = array('CabinetFile.key' => $cabinetFile['CabinetFile']['key']);
		if (! $this->deleteAll($conditions, true, true)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		return true;
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

		// CabinetFileTreeも削除 Treeビヘイビアにより子ノードのTreeデータは自動的に削除される
		$conditions = [
			'cabinet_file_key' => $key,
		];
		if (!$this->CabinetFileTree->deleteAll($conditions, true, true)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

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
			return true;
		} else {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
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
}
