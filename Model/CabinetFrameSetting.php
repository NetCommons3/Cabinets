<?php
/**
 * CabinetFrameSetting Model
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
 * CabinetFrameSetting Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Cabinets\Model
 */
class CabinetFrameSetting extends CabinetsAppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Frame' => array(
			'className' => 'Frames.Frame',
			'foreignKey' => 'frame_key',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
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
		$this->validate = Hash::merge($this->validate, array(
			'frame_key' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),
			'articles_per_page' => array(
				'number' => array(
					'rule' => array('notBlank'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),
			//'comments_per_page' => array(
			//	'number' => array(
			//		'rule' => array('notBlank'),
			//		'message' => __d('net_commons', 'Invalid request.'),
			//		'required' => true,
			//	)
			//),
		));
		return parent::beforeValidate($options);
	}

/**
 * Get CabinetFrameSetting data
 *
 * @param bool $created If True, the results of the Model::find() to create it if it was null
 * @return array CabinetFrameSetting data
 */
	public function getCabinetFrameSetting($created) {
		$conditions = array(
			'frame_key' => Current::read('Frame.key')
		);

		$cabinetFrameSetting = $this->find('first', array(
				'recursive' => -1,
				'conditions' => $conditions,
			)
		);

		if ($created && ! $cabinetFrameSetting) {
			$cabinetFrameSetting = $this->create(array(
				'frame_key' => Current::read('Frame.key'),
			));
		}

		return $cabinetFrameSetting;
	}

/**
 * Save CabinetFrameSetting
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function saveCabinetFrameSetting($data) {
		$this->loadModels([
			'CabinetFrameSetting' => 'Cabinets.CabinetFrameSetting',
		]);

		//トランザクションBegin
		$this->begin();

		//バリデーション
		$this->set($data);
		if (! $this->validates()) {
			$this->rollback();
			return false;
		}

		try {
			//登録処理
			if (! $this->save(null, false)) {
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
}
