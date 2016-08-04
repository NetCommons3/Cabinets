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
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.Trackable',
		'NetCommons.OriginalKey',
		'Workflow.Workflow',
		'Workflow.WorkflowComment',
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
						__d('cabinets', 'Filename')
					),
					//'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				],
				'filename' => [
					// is_folder falseのときは . ひとつはOK
					'rule' => ['validateFilename'],
					//'rule' => '/[^' . preg_quote('\'./?|:\<>\*"') . ']/',
					'message' => __d('cabinets', 'Invalid character for file/folder name.'),
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

		$this->validate = Hash::merge($this->validate, $validate);

		return parent::beforeValidate($options);
	}

/**
 * ファイル名検査
 *
 * @param array $check 検査対象
 * @return bool
 */
	public function validateFilename($check) {
		$filename = $check['filename'];
		if ($this->data[$this->alias]['is_folder']) {
			return !preg_match('/[' . preg_quote('\'./?|:\<>\*"', '/') . ']/', $filename);
		} else {
			return !preg_match('/[' . preg_quote('\'/?|:\<>\*"', '/') . ']/', $filename);
		}
	}

	public function validateWithOutExtFileName($check) {
		if ($this->data[$this->alias]['is_folder']) {
			return true;
		}
		// ファイルの編集時だけ拡張子抜きのファイル名が空でないかチェックする
		if ($this->data[$this->alias]['key']){
			$withOutExtFileName = $this->data[$this->alias]['withOutExtFileName'];
			return (strlen($withOutExtFileName) > 0);
		}
		return true;
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

			// treeはファイルなら常に新規INSERT フォルダだったらアップデート
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

			$this->CabinetFileTree->create();
			if (($treeData = $this->CabinetFileTree->save($data)) === false) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$savedData['CabinetFileTree'] = $treeData['CabinetFileTree'];

			// Cabinet.total_size同期
			$this->updateCabinetTotalSize($data['CabinetFile']['cabinet_id']);

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
				$this->_deleteFolder($deleteFile);
			} else {
				$this->_deleteFile($deleteFile);
			}
			$this->updateCabinetTotalSize($deleteFile['CabinetFile']['cabinet_id']);

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