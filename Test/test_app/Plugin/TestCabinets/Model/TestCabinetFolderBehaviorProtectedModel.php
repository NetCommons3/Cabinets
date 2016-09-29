<?php
/**
 * CabinetFolderBehavior::_getRootFolderConditions()テスト用Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppModel', 'Model');

/**
 * CabinetFolderBehavior::_getRootFolderConditions()テスト用Model
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\test_app\Plugin\TestCabinets\Model
 */
class TestCabinetFolderBehaviorProtectedModel extends AppModel {

/**
 * 使用ビヘイビア
 *
 * @var array
 */
	public $actsAs = array(
		'Cabinets.CabinetFolder'
	);

}
