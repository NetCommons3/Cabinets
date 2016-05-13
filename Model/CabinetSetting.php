<?php
/**
 * CabinetSetting Model
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
 * CabinetSetting Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Cabinets\Model
 */
class CabinetSetting extends CabinetsAppModel {

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
		'Blocks.BlockRolePermission',
	);

/**
 * Get cabinet setting data
 *
 * @param string $cabinetKey cabinets.key
 * @return array
 */
	public function getCabinetSetting($cabinetKey) {
		$conditions = array(
			'cabinet_key' => $cabinetKey
		);

		$cabinetSetting = $this->find(
			'first',
			array(
				'recursive' => -1,
				'conditions' => $conditions,
			)
		);

		return $cabinetSetting;
	}

/**
 * Save cabinet_setting
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function saveCabinetSetting($data) {
		$this->loadModels(
			[
				'CabinetSetting' => 'Cabinets.CabinetSetting',
			]
		);

		//トランザクションBegin
		$this->begin();

		//バリデーション
		$this->set($data);
		if (!$this->validates()) {
			$this->rollback();
			return false;
		}

		try {
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

}
