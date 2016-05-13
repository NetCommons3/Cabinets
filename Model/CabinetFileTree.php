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
class CabinetFileTree extends CabinetsAppModel {

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
		'Tree'
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'CabinetFile' => array(
			'className' => 'Cabinets.CabinetFile',
			'foreignKey' => false,
			//'conditions' => 'CabinetFileTree.cabinet_file_key=CabinetFile.key  ',
			'conditions' => 'CabinetFileTree.cabinet_file_id=CabinetFile.id  ',
			'fields' => '',
			'order' => ''
		),
	);

/**
 * beforeFind
 *
 * @param array $query クエリ
 * @return bool;
 */
	public function beforeFind($query) {
		// workflow連動でアソシエーションさせる！
		$association = [
			//'CabinetFileTree.cabinet_file_key = CabinetFile.key'
			'CabinetFileTree.cabinet_file_id = CabinetFile.id'
		];
		$cabinetFileCondition = $this->CabinetFile->getWorkflowConditions($association);

		$this->bindModel(
			[
				'belongsTo' => [
					'CabinetFile' => array(
						'className' => 'Cabinets.CabinetFile',
						'foreignKey' => false,
						'conditions' => $cabinetFileCondition,
						'fields' => '',
						'order' => ''
					),

				]
			]
		);
		return parent::beforeFind($query);
	}
}
