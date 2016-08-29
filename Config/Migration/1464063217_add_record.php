<?php
/**
 * add record
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * Class AddRecord
 */
class AddRecord extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_record';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(),
		'down' => array(),
	);

/**
 * recodes
 *
 * @var array $migration
 */
	public $records = array(
		'Plugin' => array(
			//日本語
			array(
				'language_id' => '2',
				'key' => 'cabinets',
				'namespace' => 'netcommons/cabinets',
				'name' => 'キャビネット',
				'type' => 1,
				'default_action' => 'cabinet_files/index',
				'default_setting_action' => 'cabinet_blocks/index',
				'display_topics' => 1,
				'display_search' => 1,
			),
			//英語
			array(
				'language_id' => '1',
				'key' => 'cabinets',
				'namespace' => 'netcommons/cabinets',
				'name' => 'Cabinet',
				'type' => 1,
				'default_action' => 'cabinet_files/index',
				'default_setting_action' => 'cabinet_blocks/index',
				'display_topics' => 1,
				'display_search' => 1,
			),
		),
		'PluginsRole' => array(
			array(
				'role_key' => 'room_administrator',
				'plugin_key' => 'cabinets'
			),
		),
		'PluginsRoom' => array(
			//パブリックスペース
			array('room_id' => '1', 'plugin_key' => 'cabinets'),
			//プライベートスペース
			array('room_id' => '2', 'plugin_key' => 'cabinets'),
			//グループスペース
			array('room_id' => '3', 'plugin_key' => 'cabinets'),
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
		$this->loadModels([
			'Plugin' => 'PluginManager.Plugin',
		]);
		if ($direction === 'down') {
			$this->Plugin->uninstallPlugin($this->records['Plugin'][0]['key']);
			return true;
		}
		foreach ($this->records as $model => $records) {
			if (!$this->updateRecords($model, $records)) {
				return false;
			}
		}
		return true;
	}
}
