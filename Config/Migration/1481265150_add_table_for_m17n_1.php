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
		$CabinetFilesLang = $this->generateModel('CabinetFilesLanguage');

		$schema = $CabinetFilesLang->schema();
		unset($schema['id']);
		$schemaColumns = implode(', ', array_keys($schema));

		$cabinetTable = $CabinetFilesLang->tablePrefix . 'cabinets Cabinet';
		$cabFileTable = $CabinetFilesLang->tablePrefix . 'cabinet_files CabinetFile';
		$cabFileLangTable = $CabinetFilesLang->tablePrefix . 'cabinet_files_languages CabinetFilesLanguage';

		if ($direction === 'up') {
			$sql = 'INSERT INTO ' .
						$CabinetFilesLang->tablePrefix . 'cabinet_files_languages(' . $schemaColumns . ')' .
					' SELECT' .
						' CabinetFile.id' .
						', Cabinet.key' .
						', CabinetFile.key' .
						', CabinetFile.language_id' .
						', 1' .
						', 0' .
						', CabinetFile.filename' .
						', CabinetFile.description' .
						', CabinetFile.created_user' .
						', CabinetFile.created' .
						', CabinetFile.modified_user' .
						', CabinetFile.modified' .
					' FROM ' . $cabFileTable . ', ' . $cabinetTable .
					' WHERE Cabinet.id = CabinetFile.cabinet_id';
		} else {
			$sql = 'UPDATE ' . $cabFileTable . ', ' . $cabFileLangTable .
					' SET CabinetFile.language_id = CabinetFilesLanguage.language_id, ' .
						'CabinetFile.filename = CabinetFilesLanguage.filename, ' .
						'CabinetFile.description = CabinetFilesLanguage.description' .
					' WHERE CabinetFile.id = CabinetFilesLanguage.cabinet_file_id' .
					'';
		}
		$CabinetFilesLang->query($sql);
		return true;
	}
}
