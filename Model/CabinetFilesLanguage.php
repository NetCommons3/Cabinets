<?php
/**
 * CabinetFilesLanguage Model
 *
 * @property CabinetFile $CabinetFile
 * @property Language $Language
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('CabinetsAppModel', 'Cabinets.Model');

/**
 * CabinetFilesLanguage Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Cabinets\Model
 */
class CabinetFilesLanguage extends CabinetsAppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'CabinetFile' => array(
			'className' => 'Cabinets.CabinetFile',
			'foreignKey' => 'cabinet_file_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Language' => array(
			'className' => 'M17n.Language',
			'foreignKey' => 'language_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * beforeValidate
 *
 * @param array $options Options
 * @return bool
 */
	public function beforeValidate($options = array()) {
		$validate = array(
			'cabinet_key' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'cabinet_file_key' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_origin' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_translation' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
		);

		$this->validate = Hash::merge($this->validate, $validate);

		return parent::beforeValidate($options);
	}

/**
 * キャビネットファイル言語テーブルのバインド条件を戻す
 *
 * @param string $joinKey JOINするKeyフィールド(default: CabinetFile.id)
 * @return array
 */
	public function bindModelCabinetFilesLang($joinKey = 'CabinetFile.id') {
		$belongsTo = array(
			'belongsTo' => array(
				'CabinetFilesLanguage' => array(
					'className' => 'Categories.CabinetFilesLanguage',
					'foreignKey' => false,
					'conditions' => array(
						'CabinetFilesLanguage.cabinet_file_id = ' . $joinKey,
						'OR' => array(
							'CabinetFilesLanguage.is_translation' => false,
							'CabinetFilesLanguage.language_id' => Current::read('Language.id', '0'),
						),
					),
					'fields' => array('filename', 'description', 'is_origin', 'is_translation'),
					'order' => ''
				),
			)
		);

		return $belongsTo;
	}

}
