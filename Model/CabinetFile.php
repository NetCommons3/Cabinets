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
		'Tags.Tag',
		'NetCommons.OriginalKey',
		//'NetCommons.Publishable',
		'Workflow.Workflow',
		'Likes.Like',
		'Workflow.WorkflowComment',
		//'Categories.Category',
		'Files.Attachment' => [
			'foo_photo' => [
					'thumbnailSizes' => array(
						// デフォルトはAttachmentビヘイビアできめてあるが、下記の様に設定も可能
						// NC2 800 > 640 > 480だった
							'big' => '800ml',
							'medium' => '400ml',
							'small' => '200ml',
							'thumb' => '80x80',
					),
				//'contentKeyFieldName' => 'id'
			],
			'pdf',
		],
			'AuthorizationKeys.AuthorizationKey',
		);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Category' => array(
			'className' => 'Categories.Category',
			'foreignKey' => 'category_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		//'PhotoFile' => array( // コンテンツを更新してもファイル差し替えないこともあるのでコンテンツn:ファイル1の関係
		//	'className' => 'Files.Attachment',
		//	'foreignKey' => 'photo_file_id',
		//	'conditions' => '',
		//),// 関連づけておいて、afterSaveで関連モデルのsaveする？
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


			'title' => array(
				'notBlank' => [
					'rule' => array('notBlank'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('cabinets', 'Title')),
					//'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				],
			),
			'body1' => array(
				'notBlank' => [
					'rule' => array('notBlank'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('cabinets', 'Body1')),
					//'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				],
			),
			'publish_start' => array(
				'notBlank' => [
					'rule' => array('notBlank'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('cabinets', 'Published datetime')),
					//'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				],
			),
			'category_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					//'message' => 'Your custom message here',
					'allowEmpty' => true,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			//'key' => array(
			//	'notBlank' => array(
			//		'rule' => array('notBlank'),
			//		//'message' => 'Your custom message here',
			//		//'allowEmpty' => false,
			//		//'required' => false,
			//		//'last' => false, // Stop validation after this rule
			//		//'on' => 'create', // Limit validation to 'create' or 'update' operations
			//	),
			//),
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
				'group' => array('CabinetFile__year_month'), //GROUP BY YEAR(record_date), MONTH(record_date)
			)
		);
		// 使ったバーチャルFieldを削除
		unset($this->virtualFields['year_month']);
		unset($this->virtualFields['count']);

		$ret = array();
		// $retをゼロ埋め
		//　一番古いファイルを取得
		$oldestFile = $this->find('first',
			array(
				'conditions' => $conditions,
				'order' => 'publish_start ASC',
			)
		);

		// 一番古いファイルの年月から現在までを先にゼロ埋め
		if (isset($oldestFile['CabinetFile'])) {
			$currentYearMonthDay = date('Y-m-01', strtotime($oldestFile['CabinetFile']['publish_start']));
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
 * ファイルの保存。タグも保存する
 *
 * @param int $blockId ブロックID
 * @param int $frameId frame ID
 * @param array $data 登録データ
 * @return bool
 * @throws InternalErrorException
 */
	public function saveFile($blockId, $frameId, $data) {
		$this->begin();
		try {
			$this->create(); // 常に新規登録
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
 * @param int $key オリジンID
 * @throws InternalErrorException
 * @return bool
 */
	public function deleteFileByKey($key) {
		// ε(　　　　 v ﾟωﾟ)　＜タグリンク削除
		$this->begin();
		try{
			//コメントの削除
			$deleteFile = $this->findByKey($key);
			//コメントの削除
			$this->deleteCommentsByContentKey($deleteFile['CabinetFile']['key']);

			// ファイル削除
			$conditions = array('key' => $key);
			if ($result = $this->deleteAll($conditions, true, true)) {
				$this->commit();
				return $result;
			} else {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		} catch (Exception $e) {
			$this->rollback();
			//エラー出力
			CakeLog::error($e);
			throw $e;
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

}
