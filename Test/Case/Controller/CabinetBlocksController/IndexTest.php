<?php
/**
 * CabinetBlocksController::index()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('BlocksControllerTest', 'Blocks.TestSuite');

/**
 * CabinetBlocksController::index()のテスト
 *
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @package NetCommons\Cabinets\Test\Case\Controller\CabinetBlocksController
 */
class CabinetBlocksControllerIndexTest extends BlocksControllerTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet',
		'plugin.cabinets.cabinet_file',
		'plugin.cabinets.cabinet_file_tree',
		'plugin.cabinets.block_setting_for_cabinet',
		'plugin.workflow.workflow_comment',
		'plugin.authorization_keys.authorization_keys',
		'plugin.site_manager.site_setting',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'cabinets';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'cabinet_blocks';

/**
 * Edit controller name
 *
 * @var string
 */
	protected $_editController = 'cabinet_blocks';

}
