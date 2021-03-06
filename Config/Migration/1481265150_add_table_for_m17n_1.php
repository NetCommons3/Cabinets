<?php
/**
 * 多言語化対応
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * 多言語化対応
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Cabinets\Config\Migration
 */
class AddTableForM17n1 extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_table_for_m17n_1';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
		),
		'down' => array(
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		$CabinetFile = $this->generateModel('CabinetFile');

		$cabFileTable = $CabinetFile->tablePrefix . 'cabinet_files CabinetFile';
		$cabFileTreeTable = $CabinetFile->tablePrefix . 'cabinet_file_trees CabinetFileTree';

		if ($direction === 'up') {
			$sql = 'UPDATE ' . $cabFileTable . ', ' . $cabFileTreeTable .
					' SET CabinetFile.cabinet_file_tree_id = CabinetFileTree.id' .
					', CabinetFile.cabinet_key = CabinetFileTree.cabinet_key' .
					' WHERE CabinetFile.id = CabinetFileTree.cabinet_file_id' .
					'';
			$CabinetFile->query($sql);
		}
		return true;
	}
}
