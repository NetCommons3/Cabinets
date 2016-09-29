<?php
class AddSizeField extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_size_field';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'cabinet_files' => array(
					'size' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'after' => 'use_auth_key'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'cabinet_files' => array('size'),
			),
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
		if ($direction === 'up') {
			// CabinetFileを取得してsizeフィールドを埋める。
			// is_activeとis_latestなis_folder=falseなCabinetFileデータだけでもよいかも
			$CabinetFile = ClassRegistry::init('Cabinets.CabinetFile');

			$cabinetFiles = $CabinetFile->find('all', ['conditions' => ['1' => '1']]);
			foreach ($cabinetFiles as $file) {
				$file['CabinetFile']['size'] = Hash::get($file, 'UploadFile.file.size', 0);
				$CabinetFile->create();
				$CabinetFile->save($file, ['validate' => false, 'callbacks' => false]);
			}
		}
		return true;
	}
}
