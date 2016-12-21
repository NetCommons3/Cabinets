<?php
/**
 * CabinetFilesLanguage Test Case
 *
* @author Noriko Arai <arai@nii.ac.jp>
* @author Your Name <yourname@domain.com>
* @link http://www.netcommons.org NetCommons Project
* @license http://www.netcommons.org/license.txt NetCommons License
* @copyright Copyright 2014, NetCommons Project
 */

App::uses('CabinetFilesLanguage', 'Cabinets.Model');

/**
 * Summary for CabinetFilesLanguage Test Case
 */
class CabinetFilesLanguageTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet_files_language',
		'plugin.cabinets.cabinet_file',
		'plugin.cabinets.user',
		'plugin.cabinets.role',
		'plugin.cabinets.user_role_setting',
		'plugin.cabinets.users_language',
		'plugin.cabinets.language'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->CabinetFilesLanguage = ClassRegistry::init('Cabinets.CabinetFilesLanguage');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CabinetFilesLanguage);

		parent::tearDown();
	}

}
