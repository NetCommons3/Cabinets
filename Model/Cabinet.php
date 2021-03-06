<?php
/**
 * Cabinet Model
 *
 * @property Block $Block
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('CabinetsAppModel', 'Cabinets.Model');

/**
 * Cabinet Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Cabinets\Model
 */
class Cabinet extends CabinetsAppModel {

/**
 * use tables
 *
 * @var string
 */
	public $useTable = 'cabinets';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Blocks.Block' => array(
			'name' => 'Cabinet.name',
			'loadModels' => array(
				'CabinetSetting' => 'Cabinets.CabinetSetting',
				'WorkflowComment' => 'Workflow.WorkflowComment',
			)
		),
		'NetCommons.OriginalKey',
		//多言語
		'M17n.M17n' => array(
			'keyField' => 'block_id'
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Block' => array(
			'className' => 'Blocks.Block',
			'foreignKey' => 'block_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		//'CabinetFile' => [
		//	'className' => 'Cabinets.CabinetFile'
		//]
	);

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		$this->validate = ValidateMerge::merge(
			$this->validate,
			array(
				//'block_id' => array(
				//	'numeric' => array(
				//		'rule' => array('numeric'),
				//		'message' => __d('net_commons', 'Invalid request.'),
				//		//'allowEmpty' => false,
				//		//'required' => true,
				//	)
				//),
				'key' => array(
					'notBlank' => array(
						'rule' => array('notBlank'),
						'message' => __d('net_commons', 'Invalid request.'),
						'allowEmpty' => false,
						'required' => true,
						'on' => 'update', // Limit validation to 'create' or 'update' operations
					),
				),

				//status to set in PublishableBehavior.

				'name' => array(
					'notBlank' => array(
						'rule' => array('notBlank'),
						'message' => sprintf(
							__d('net_commons', 'Please input %s.'),
							__d('cabinets', 'Cabinet name')
						),
						'required' => true
					),
				),
			)
		);

		if (!parent::beforeValidate($options)) {
			return false;
		}

		if (isset($this->data['CabinetSetting'])) {
			$this->CabinetSetting->set($this->data['CabinetSetting']);
			if (!$this->CabinetSetting->validates()) {
				$this->validationErrors = Hash::merge(
					$this->validationErrors,
					$this->CabinetSetting->validationErrors
				);
				return false;
			}
		}

		//if (isset($this->data['CabinetFrameSetting']) && ! $this->data['CabinetFrameSetting']['id']) {
		//	$this->CabinetFrameSetting->set($this->data['CabinetFrameSetting']);
		//	if (! $this->CabinetFrameSetting->validates()) {
		//		$this->validationErrors = Hash::merge($this->validationErrors, $this->CabinetFrameSetting->validationErrors);
		//		return false;
		//	}
		//}
	}

/**
 * Called after each successful save operation.
 *
 * @param bool $created True if this save created a new record
 * @param array $options Options passed from Model::save().
 * @return void
 * @throws InternalErrorException
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#aftersave
 * @see Model::save()
 */
	public function afterSave($created, $options = array()) {
		$this->loadModels([
			'CabinetFile' => 'Cabinets.CabinetFile',
		]);
		//CabinetSetting登録
		if (isset($this->CabinetSetting->data['CabinetSetting'])) {
			$this->CabinetSetting->set($this->CabinetSetting->data['CabinetSetting']);
			$this->CabinetSetting->save(null, false);
		}

		// ルートフォルダがまだなければルートフォルダをつくる。あれば名前の同期
		if (!$this->CabinetFile->syncRootFolder($this->data)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

		parent::afterSave($created, $options);
	}

/**
 * Create cabinet data
 *
 * @return array
 */
	public function createCabinet() {
		$this->CabinetSetting = ClassRegistry::init('Cabinets.CabinetSetting');

		$cabinet = $this->createAll(
			array(
				'Cabinet' => array(
					'name' => __d('cabinets', 'New cabinet %s', date('YmdHis')),
				),
				'Block' => array(
					'room_id' => Current::read('Room.id'),
					'language_id' => Current::read('Language.id'),
				),
			)
		);
		$cabinet = Hash::merge($cabinet, $this->CabinetSetting->createBlockSetting());

		return $cabinet;
	}

/**
 * Get cabinet data
 *
 * @return array
 */
	public function getCabinet() {
		$this->CabinetSetting = ClassRegistry::init('Cabinets.CabinetSetting');

		$cabinet = $this->find(
			'all',
			array(
				'recursive' => 0,
				//'fields' => array(
				//	$this->alias . '.*',
				//	$this->Block->alias . '.*',
				//),
				//'joins' => array(
				//	array(
				//		'table' => $this->Block->table,
				//		'alias' => $this->Block->alias,
				//		'type' => 'INNER',
				//		'conditions' => array(
				//			$this->alias . '.block_id' . ' = ' . $this->Block->alias . ' .id',
				//		),
				//	),
				//),
				'conditions' => $this->getBlockConditionById(),
			)
		);
		if (!$cabinet) {
			return $cabinet;
		}
		return Hash::merge($cabinet[0], $this->CabinetSetting->getCabinetSetting());
	}

/**
 * Save cabinets
 *
 * @param array $data received post data
 * @return bool True on success, false on validation errors
 * @throws InternalErrorException
 */
	public function saveCabinet($data) {
		$this->loadModels(
			[
				'Cabinet' => 'Cabinets.Cabinet',
				'CabinetSetting' => 'Cabinets.CabinetSetting',
				//'CabinetFrameSetting' => 'Cabinets.CabinetFrameSetting',
				'CabinetFile' => 'Cabinets.CabinetFile'
			]
		);

		//トランザクションBegin
		$this->begin();

		//バリデーション
		$this->set($data);
		if (!$this->validates()) {
			return false;
		}

		try {
			//登録処理
			if (!$this->save(null, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}

		return true;
	}

/**
 * Delete cabinets
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function deleteCabinet($data) {
		$this->loadModels(
			[
				'Cabinet' => 'Cabinets.Cabinet',
				'CabinetFile' => 'Cabinets.CabinetFile',
				'CabinetFileTree' => 'Cabinets.CabinetFileTree',
			]
		);

		//トランザクションBegin
		$this->begin();

		try {
			if (!$this->deleteAll(
				array($this->alias . '.key' => $data['Cabinet']['key']),
				false,
				false
			)
			) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			// アップロードファイルの削除をしたいのでコールバック有効にする
			if (!$this->CabinetFile->deleteAll(
				array($this->CabinetFile->alias . '.cabinet_key' => $data['Cabinet']['key']),
				true,
				true
			)
			) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			if (!$this->CabinetFileTree->deleteAll(
				array($this->CabinetFileTree->alias . '.cabinet_key' => $data['Cabinet']['key']),
				false
			)
			) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//Blockデータ削除
			$this->deleteBlock($data['Block']['key']);

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}

		return true;
	}

}
