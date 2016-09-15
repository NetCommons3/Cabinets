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
 * @return array クエリ
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
		// recursive 0以上の時だけにする NOT NULL 条件を追加する
		$recursive = Hash::get($query, 'recursive', $this->recursive);
		if ($recursive >= 0) {
			// CabinetFileがLEFT JOIN されるが、
			// JOINできないTreeレコードを切り捨てるためにCabinetFile.id NOT NULLを条件に入れる
			$query['conditions']['NOT']['CabinetFile.id'] = null;
		}
		return $query;
	}

/**
 * modifiedを常に更新
 *
 * @param null $data 登録データ
 * @param bool $validate バリデートを実行するか
 * @param array $fieldList フィールド
 * @return mixed
 *
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function save($data = null, $validate = true, $fieldList = array()) {
		// 保存前に modified フィールドをクリアする
		$this->set($data);
		if (isset($this->data[$this->alias]['modified'])) {
			unset($this->data[$this->alias]['modified']);
		}
		return parent::save($this->data, $validate, $fieldList);
	}
}
